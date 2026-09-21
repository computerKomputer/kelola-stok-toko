<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\StagedSaleService;
use Illuminate\Http\Request;
class StagedSaleController {
 public function pay(Request $r,Order $order,StagedSaleService $service){$d=$r->validate(['amount'=>'required|integer|min:1|max:100000000000000','method'=>'required|in:cash,transfer','token'=>'required|uuid']);$service->pay($order,$r->user(),(int)$d['amount'],$d['method'],$d['token']);return back()->with('success','Pembayaran dicatat.');}
 public function fulfill(Request $r,Order $order,StagedSaleService $service){$service->fulfill($order,$r->user());return back()->with('success','Barang digunakan/diserahkan dan pekerjaan selesai dicatat.');}
 public function cancel(Request $r,Order $order,StagedSaleService $service){$d=$r->validate(['refund_mode'=>'required|in:full,partial','fee'=>'required_if:refund_mode,partial|nullable|integer|min:1|max:100000000000000','method'=>'required|in:cash,transfer','note'=>'required|string|max:500']);$service->cancel($order,$r->user(),$d['method'],$d['note'],$d['refund_mode']==='partial'?(int)$d['fee']:0);return back()->with('success','Transaksi dibatalkan, reservasi dilepas, pengembalian uang dicatat.');}
}
