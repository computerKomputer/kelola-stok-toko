<?php
namespace App\Http\Controllers;
use App\Models\{Product,Order,OrderItem,User};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class DirectSaleController {
 public function store(Request $r,InventoryService $inventory){
  $d=$r->validate(['customer_name'=>'required|string|max:100','work_description'=>'nullable|string','checkout_token'=>'required|uuid','payment_mode'=>'sometimes|required|in:full,deposit','deposit'=>'required_if:payment_mode,deposit|nullable|integer|min:1|max:100000000000000','payment_method'=>'required|in:cash,transfer','items'=>'required|array|min:1|max:50','items.*.type'=>'required|in:product,service','items.*.quantity'=>'required|integer|min:1|max:1000','items.*.product_id'=>'nullable|integer','items.*.name'=>'nullable|string|max:150','items.*.price'=>'nullable|integer|min:0|max:1000000000']);
  $order=DB::transaction(function()use($r,$d,$inventory){
   User::whereKey($r->user()->id)->lockForUpdate()->firstOrFail();
   if($existing=Order::where('checkout_token',$d['checkout_token'])->first()){abort_unless($existing->user_id===$r->user()->id,403);return $existing;}
   $cart=[];$lines=[];$total=0;
   foreach($d['items'] as $index=>$line){
    if($line['type']==='product'){
     if(empty($line['product_id']))throw ValidationException::withMessages(['items.'.$index.'.product_id'=>'Pilih produk.']);
     $cart[$line['product_id']]=($cart[$line['product_id']]??0)+(int)$line['quantity'];
    }else{
     if(empty(trim($line['name']??'')) || !isset($line['price']))throw ValidationException::withMessages(['items.'.$index.'.name'=>'Isi nama jasa dan tarifnya.']);
     $lines[]=['item_type'=>'service','product_id'=>null,'product_name'=>$line['name'],'quantity'=>$line['quantity'],'unit_price'=>$line['price'],'unit_cost'=>0,'cost_confirmed'=>false];
     $total+=(int)$line['quantity']*(int)$line['price'];
    }
   }
   ksort($cart);$products=Product::whereIn('id',array_keys($cart))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
   foreach($cart as $id=>$qty){$p=$products->get($id);if(!$p || !$p->active || $qty>1000 || $p->available_stock<$qty)throw ValidationException::withMessages(['items'=>'Produk tidak tersedia atau stok tidak mencukupi.']);
    $lines[]=['item_type'=>'product','product_id'=>$id,'product_name'=>$p->name,'quantity'=>$qty,'unit_price'=>$p->sale_price,'unit_cost'=>$p->cost_price,'cost_confirmed'=>($p->cost_confirmed || $p->cost_price>0)];$total+=$qty*$p->sale_price;
   }
   $isDeposit=($d['payment_mode']??'full')==='deposit';
   $initial=$isDeposit?(int)$d['deposit']:$total;
   if($isDeposit && $initial>=$total)throw ValidationException::withMessages(['deposit'=>'DP harus lebih kecil dari total tagihan. Pilih lunas jika dibayar penuh.']);
   $order=Order::create(['number'=>'INV-'.strtoupper(bin2hex(random_bytes(5))),'checkout_token'=>$d['checkout_token'],'user_id'=>$r->user()->id,'customer_name'=>$d['customer_name'],'phone'=>'','work_description'=>$d['work_description']??null,'address'=>'Penjualan langsung di toko','total'=>$total,'payment_method'=>$d['payment_method'],'staged_sale'=>true,'status'=>$isDeposit?'processing':'completed','paid_at'=>$isDeposit?null:now(),'completed_at'=>$isDeposit?null:now(),'fulfilled_at'=>$isDeposit?null:now()]);
   foreach($lines as $line)$order->items()->create($line);
   foreach($cart as $id=>$qty){if($isDeposit){$products[$id]->reserved_stock+=$qty;$products[$id]->save();}else{$inventory->move($products[$id],-$qty,$r->user(),'sale',$order->number);}}
   \App\Models\Payment::create(['order_id'=>$order->id,'user_id'=>$r->user()->id,'token'=>'initial-'.$d['checkout_token'],'amount'=>$initial,'method'=>$d['payment_method'],'received_at'=>now(),'note'=>$isDeposit?'DP':'Pembayaran lunas']);
   return $order;
  },3);
  return redirect('/orders/'.$order->id)->with('success','Penjualan dan pembayaran berhasil dicatat.');
 }
 public function costForm(OrderItem $item){return view('admin.item-cost',['item'=>$item,'revisions'=>DB::table('cost_revisions')->join('users','users.id','=','cost_revisions.user_id')->where('order_item_id',$item->id)->select('cost_revisions.*','users.name')->latest('cost_revisions.id')->paginate(10,["*"],"revision_page")->withQueryString()]);}
 public function costSave(Request $r,OrderItem $item){
  $d=$r->validate(['unit_cost'=>'required|integer|min:0|max:1000000000','note'=>'required|string|max:500']);
  DB::transaction(function()use($item,$d,$r){$locked=OrderItem::whereKey($item->id)->lockForUpdate()->firstOrFail();DB::table('cost_revisions')->insert(['user_id'=>$r->user()->id,'product_id'=>$locked->product_id,'order_item_id'=>$locked->id,'old_cost'=>$locked->unit_cost,'new_cost'=>$d['unit_cost'],'note'=>$d['note'],'created_at'=>now(),'updated_at'=>now()]);$locked->update(['unit_cost'=>$d['unit_cost'],'cost_confirmed'=>true]);});
  return redirect('/orders/'.$item->order_id)->with('success','Modal / biaya transaksi diperbarui tanpa mengubah omzet.');
 }
}
