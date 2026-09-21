<?php
namespace App\Services;
use App\Models\{Order,Product,Payment,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class StagedSaleService {
 private function error($text){throw ValidationException::withMessages(['transaction'=>$text]);}
 private function completeIfReady(Order $order):void {
  if($order->fulfilled_at && $order->balance===0){$order->status='completed';$order->completed_at??=now();}
  if($order->balance===0)$order->paid_at??=now();
  $order->save();
 }
 public function pay(Order $order,User $user,int $amount,string $method,string $token):void {
  DB::transaction(function()use($order,$user,$amount,$method,$token){
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
   abort_unless($order->staged_sale,422);
   if($existing=Payment::where('token',$token)->first()){abort_unless($existing->order_id===$order->id,403);return;}
   if($order->status==='cancelled')$this->error('Transaksi sudah dibatalkan.');
   if($amount<1 || $amount>$order->balance)$this->error('Pembayaran harus lebih dari nol dan tidak melebihi sisa tagihan.');
   Payment::create(['order_id'=>$order->id,'user_id'=>$user->id,'token'=>$token,'amount'=>$amount,'method'=>$method,'received_at'=>now(),'note'=>'Pembayaran lanjutan']);
   $this->completeIfReady($order);
  },3);
 }
 public function fulfill(Order $order,User $user):void {
  DB::transaction(function()use($order,$user){
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();abort_unless($order->staged_sale,422);
   if($order->status==='cancelled')$this->error('Transaksi sudah dibatalkan.');
   if($order->fulfilled_at)return;
   foreach($order->items()->whereNotNull('product_id')->orderBy('product_id')->get() as $item){
    $p=Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
    if($p->reserved_stock<$item->quantity || $p->stock<$item->quantity)$this->error('Stok reservasi tidak mencukupi.');
    $p->reserved_stock-=$item->quantity;
    app(InventoryService::class)->move($p,-$item->quantity,$user,'sale',$order->number,'Barang digunakan / diserahkan');
   }
   $order->fulfilled_at=now();$this->completeIfReady($order);
  },3);
 }
 public function cancel(Order $order,User $user,string $method,string $note,int $fee=0):void {
  abort_unless(in_array($user->role,['admin','owner'],true),403);
  DB::transaction(function()use($order,$user,$method,$note,$fee){
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();abort_unless($order->staged_sale,422);
   if($order->status==='cancelled')return;
   if($order->fulfilled_at)$this->error('Barang sudah digunakan/diserahkan. Pembatalan otomatis tidak tersedia; perlu pemeriksaan retur.');
   $paid=$order->paid_amount;
   if($fee<0 || ($fee>0 && $fee>=$paid))$this->error('Biaya pengerjaan harus lebih kecil dari pembayaran yang diterima agar ada pengembalian sebagian.');
   foreach($order->items()->whereNotNull('product_id')->orderBy('product_id')->get() as $item){$p=Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();if($p->reserved_stock<$item->quantity)$this->error('Reservasi tidak sesuai.');$p->reserved_stock-=$item->quantity;$p->save();}
   $refund=$paid-$fee;
   if($refund>0)Payment::create(['order_id'=>$order->id,'user_id'=>$user->id,'token'=>'refund-'.$order->id,'amount'=>-$refund,'method'=>$method,'received_at'=>now(),'note'=>'Pengembalian DP. Biaya pengerjaan Rp '.$fee.'. '.$note]);
   $feeOrder=null;
   if($fee>0){
    // A linked service sale recognizes the retained fee, but creates no new cash receipt.
    $feeOrder=Order::create(['number'=>'FEE-'.strtoupper(bin2hex(random_bytes(5))),'checkout_token'=>(string)\Illuminate\Support\Str::uuid(),'user_id'=>$user->id,'customer_name'=>$order->customer_name,'phone'=>$order->phone,'address'=>$order->address,'work_description'=>'Biaya pengerjaan dari pembatalan '.$order->number.'. Dibayar dari potongan DP, bukan penerimaan uang baru. '.$note,'total'=>$fee,'settlement_credit'=>$fee,'payment_method'=>$order->payment_method,'status'=>'completed','paid_at'=>now(),'fulfilled_at'=>now(),'completed_at'=>now()]);
    $feeOrder->items()->create(['product_id'=>null,'item_type'=>'service','product_name'=>'Biaya pengerjaan / pemeriksaan','quantity'=>1,'unit_price'=>$fee,'unit_cost'=>0,'cost_confirmed'=>false]);
   }
   DB::table('sale_cancellations')->insert(['order_id'=>$order->id,'user_id'=>$user->id,'fee_order_id'=>$feeOrder?->id,'paid_before'=>$paid,'fee'=>$fee,'refund'=>$refund,'method'=>$method,'note'=>$note,'created_at'=>now(),'updated_at'=>now()]);
   $order->update(['status'=>'cancelled']);
  },3);
 }
}
