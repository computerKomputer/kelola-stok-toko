<?php
namespace App\Http\Controllers;
use App\Models\{Expense,ExpenseCategory,Payment,Product,Supplier,User};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\{Rule,ValidationException};
class ExpenseController {
 public function index(Request $r){
  $d=$r->validate(['q'=>'nullable|string|max:150','from'=>'nullable|date_format:Y-m-d','to'=>'nullable|date_format:Y-m-d|after_or_equal:from','category'=>['nullable',Rule::in(array_merge(array_keys(ExpenseCategory::labels()),['refund']))]]);
  $from=$d['from']??today()->startOfMonth()->toDateString();$to=$d['to']??today()->toDateString();$category=$d['category']??'';
  $expenses=Expense::whereDate('spent_on','>=',$from)->whereDate('spent_on','<=',$to);$refunds=Payment::where('amount','<',0)->whereBetween('received_at',[$from.' 00:00:00',$to.' 23:59:59']);
  if(!$r->user()->isOwner()){ $expenses->where('user_id',$r->user()->id); $refunds->where('user_id',$r->user()->id); }
  $search=trim($d['q']??'');
  if($search!==''){
   $expenses->where(function($q)use($search){
    $q->where('recipient','like','%'.$search.'%')->orWhere('note','like','%'.$search.'%')->orWhereHas('supplier',fn($s)=>$s->where('name','like','%'.$search.'%'))->orWhereHas('product',fn($p)=>$p->where('name','like','%'.$search.'%')->orWhere('sku','like','%'.$search.'%'));
    if(preg_match('/^(?:EXP-)?([0-9]+)$/i',$search,$m))$q->orWhere('id',$m[1]);
   });
   $refunds->where(function($q)use($search){$q->where('note','like','%'.$search.'%')->orWhereHas('order',fn($o)=>$o->where('number','like','%'.$search.'%')->orWhere('customer_name','like','%'.$search.'%'));});
  }
  $totals=[];foreach(ExpenseCategory::labels() as $key=>$label)$totals[$label]=(clone $expenses)->where('category',$key)->sum('amount');$totals['Pengembalian DP']=-(clone $refunds)->sum('amount');
  if($category==='refund')$expenses->whereRaw('1=0');elseif($category)$expenses->where('category',$category);
  if($category && $category!=='refund')$refunds->whereRaw('1=0');
  return view('admin.expenses',['from'=>$from,'to'=>$to,'category'=>$category,'totals'=>$totals,'expenses'=>$expenses->with('user','product','supplier')->latest('spent_on')->latest('id')->paginate(10,['*'],'expense_page')->withQueryString(),'refunds'=>$refunds->with('order')->latest('received_at')->paginate(10,['*'],'refund_page')->withQueryString()]);
 }
 public function create(){return view('admin.expense-form',['products'=>Product::orderBy('name')->get(),'suppliers'=>Supplier::orderBy('name')->get()]);}
 public function store(Request $r,InventoryService $inventory){
  $d=$r->validate(['token'=>'required|uuid','category'=>['required',Rule::in(array_keys(ExpenseCategory::labels(true)))],'amount'=>'required|integer|min:0|max:1000000000000','method'=>'required|in:cash,transfer','spent_on'=>'required|date_format:Y-m-d|before_or_equal:today','recipient'=>'required|string|max:150','note'=>'required|string|max:2000','supplier_id'=>'nullable|exists:suppliers,id','stock_action'=>'required|in:none,add,remove','product_id'=>'nullable|exists:products,id','quantity'=>'nullable|integer|min:1|max:1000000']);
  abort_if(!$r->user()->isOwner() && $d['stock_action']==='remove',403);
  if(($d['category']!=='purchase' && $d['stock_action']==='add') || ($d['category']!=='internal' && $d['stock_action']==='remove'))throw ValidationException::withMessages(['stock_action'=>'Perubahan stok tidak sesuai kategori pengeluaran.']);
  if($d['amount']==0 && !($d['category']==='internal' && $d['stock_action']==='remove'))throw ValidationException::withMessages(['amount'=>'Isi jumlah uang yang benar-benar dibayarkan.']);
  if($d['category']==='purchase' && empty($d['supplier_id']))throw ValidationException::withMessages(['supplier_id'=>'Pilih supplier pembelian.']);
  if($d['stock_action']!=='none' && (empty($d['product_id']) || empty($d['quantity'])))throw ValidationException::withMessages(['quantity'=>'Pilih produk dan jumlah perubahan stok.']);
  DB::transaction(function()use($r,$d,$inventory){
   User::whereKey($r->user()->id)->lockForUpdate()->firstOrFail();
   if(Expense::where('token',$d['token'])->exists())return;
   $change=$d['stock_action']==='none'?0:(int)$d['quantity']*($d['stock_action']==='remove'?-1:1);
   $product=$change?Product::whereKey($d['product_id'])->lockForUpdate()->firstOrFail():null;
   if($product && $product->stock+$change>1000000)throw ValidationException::withMessages(['quantity'=>'Total stok maksimal 1.000.000 unit.']);
   $expense=Expense::create(['token'=>$d['token'],'user_id'=>$r->user()->id,'category'=>$d['category'],'amount'=>$d['amount'],'method'=>$d['method'],'spent_on'=>$d['spent_on'],'recipient'=>$d['recipient'],'note'=>$d['note'],'supplier_id'=>$d['category']==='purchase'?$d['supplier_id']:null,'product_id'=>$product?->id,'stock_change'=>$change]);
   if($product)$inventory->move($product,$change,$r->user(),'adjustment','EXP-'.$expense->id,$d['note']);
  },3);
  return redirect('/manage/expenses')->with('success','Pengeluaran dicatat. Perubahan stok yang dipilih sudah diterapkan.');
 }
}
