<?php
namespace App\Services;
use App\Models\{Product,Order,Purchase,PurchaseItem,StockMovement,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class InventoryService {
 public function move(Product $product,int $quantity,User $user,string $type,string $reference,?string $note=null): void {
  if($type==='adjustment' && $quantity<0)abort_unless($user->isOwner(),403,'Pengurangan stok hanya untuk Superadmin.');
  if($product->stock+$quantity<$product->reserved_stock) throw ValidationException::withMessages(['stock'=>'Stok tidak mencukupi untuk '.$product->name.'.']);
  $product->stock += $quantity; $product->save();
  StockMovement::create(['product_id'=>$product->id,'user_id'=>$user->id,'quantity'=>$quantity,'balance'=>$product->stock,'type'=>$type,'reference'=>$reference,'note'=>$note]);
 }
 public function checkout(User $user,array $cart,array $data): Order {
  return DB::transaction(function()use($user,$cart,$data){
   // Lock the customer to serialize duplicate submissions, including an empty initial token lookup.
   User::whereKey($user->id)->lockForUpdate()->firstOrFail();
   $existing=Order::where('checkout_token',$data['checkout_token'])->first();
   if($existing){abort_unless($existing->user_id===$user->id,403);return $existing;}
   if(!$cart) throw ValidationException::withMessages(['cart'=>'Keranjang masih kosong.']);
   ksort($cart);$products=Product::whereIn('id',array_keys($cart))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
   $total=0;
   foreach($cart as $id=>$qty){
    $p=$products->get($id);
    if(!$p || !$p->active || $qty<1 || $qty>1000 || $p->available_stock<$qty) throw ValidationException::withMessages(['cart'=>'Produk tidak tersedia atau stok berubah. Periksa keranjang Anda.']);
    $total += $p->sale_price*$qty;
   }
   $order=Order::create(['number'=>'INV-'.strtoupper(bin2hex(random_bytes(5))),'checkout_token'=>$data['checkout_token'],'user_id'=>$user->id,'customer_name'=>$data['customer_name'],'phone'=>$data['phone'],'address'=>$data['address'],'total'=>$total,'payment_method'=>'cod']);
   foreach($cart as $id=>$qty){$p=$products[$id];$order->items()->create(['product_id'=>$id,'product_name'=>$p->name,'quantity'=>$qty,'unit_price'=>$p->sale_price,'unit_cost'=>$p->cost_price,'cost_confirmed'=>($p->cost_confirmed || $p->cost_price>0)]);$this->move($p,-$qty,$user,'sale',$order->number);}
   return $order;
  },3);
 }
 public function receive(Purchase $purchase,array $quantities,User $user,?string $token=null): void {
  DB::transaction(function()use($purchase,$quantities,$user,$token){
   $purchase=Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();
   if($token && ($submission=DB::table('receipt_submissions')->where('token',$token)->first())){abort_unless((int)$submission->purchase_id===$purchase->id,403);return;}
   $items=$purchase->items()->orderBy('product_id')->lockForUpdate()->get();$totalQty=$items->sum('quantity');$changed=false;
   foreach($quantities as $id=>$qty) if(!$items->contains('id',(int)$id)) throw ValidationException::withMessages(['quantities'=>'Item penerimaan tidak valid.']);
   foreach($items as $item){
    $qty=(int)($quantities[$item->id]??0);if($qty===0)continue;
    if($qty<0 || $qty>$item->quantity-$item->received)throw ValidationException::withMessages(['quantities'=>'Jumlah diterima melebihi sisa pesanan.']);
    $p=Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
    $landed=$item->unit_cost+(int)round($purchase->extra_cost/$totalQty);
    $p->cost_price=(int)round(($p->stock*$p->cost_price+$qty*$landed)/($p->stock+$qty));
    $this->move($p,$qty,$user,'receipt',$purchase->number);
    $item->increment('received',$qty);$changed=true;
   }
   if(!$changed)throw ValidationException::withMessages(['quantities'=>'Masukkan setidaknya satu barang yang diterima.']);
   $purchase->status=$purchase->items()->whereColumn('received','<','quantity')->exists()?'partial':'received';$purchase->save();
   if($token)DB::table('receipt_submissions')->insert(['token'=>$token,'purchase_id'=>$purchase->id,'created_at'=>now()]);
  },3);
 }
 public function transition(Order $order,string $status,User $user): void {
  DB::transaction(function()use($order,$status,$user){
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
   abort_if($order->staged_sale,422,'Gunakan tombol pembayaran dan penyelesaian pada transaksi DP.');
   $allowed=['pending'=>['processing','cancelled'],'processing'=>['shipped','cancelled'],'shipped'=>['completed'],'completed'=>[],'cancelled'=>[]];
   if(!in_array($status,$allowed[$order->status]??[],true))throw ValidationException::withMessages(['status'=>'Perubahan status tidak diperbolehkan.']);
   if($status==='cancelled'){
    foreach($order->items()->whereNotNull('product_id')->orderBy('product_id')->get() as $item){
     $p=Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
     // Manual product cost is preserved when a sale is cancelled.
     $this->move($p,$item->quantity,$user,'cancel',$order->number);
    }
   }
   if($status==='completed')\App\Models\Payment::create(['order_id'=>$order->id,'user_id'=>$user->id,'token'=>'completion-'.$order->id,'amount'=>$order->total,'method'=>$order->payment_method==='transfer'?'transfer':'cash','received_at'=>now()]);
   $order->update(['status'=>$status]+($status==='completed'?['paid_at'=>now(),'fulfilled_at'=>now(),'completed_at'=>now()]:[]));
  },3);
 }
}
