<?php
namespace App\Http\Controllers;
use App\Models\{Product,Order,OrderItem,Article,StockMovement,Setting,User,ProductCategory};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class AdminController {
 public function dashboard(Request $r){
  if($r->user()->role==='customer')return redirect('/orders');
  if($r->user()->role==='writer')return redirect('/manage/articles');
  $revenue=$r->user()->isOwner()?Order::where('status','completed')->sum('total'):\App\Models\Payment::today()->sum('amount');
  $data=['revenue'=>$revenue,'pending'=>Order::whereIn('status',['pending','processing'])->count(),'productCount'=>Product::count(),'lowCount'=>Product::whereColumn('stock','<=','min_stock')->count(),'lowStock'=>Product::whereColumn('stock','<=','min_stock')->take(5)->get(),'orders'=>Order::latest('id')->take(5)->get(),'movements'=>StockMovement::with('product')->latest('id')->take(5)->get()];
  if($r->user()->isOwner())$data['profit']=$revenue-OrderItem::whereHas('order',fn($q)=>$q->where('status','completed'))->sum(DB::raw('quantity * unit_cost'));
  return view('admin.dashboard',$data);
 }
 public function products(Request $r){return view('admin.products',['products'=>Product::when($r->q,fn($q)=>$q->where('name','like','%'.mb_substr($r->q,0,100).'%'))->latest()->paginate(10)->withQueryString()]);}
 public function productForm(?Product $product=null){return view('admin.product-form',['product'=>$product??new Product(),'categories'=>ProductCategory::orderBy('name')->get(),'discountLimit'=>Setting::valueOf('admin_discount_limit','10')]);}
 public function saveProduct(Request $r,?Product $product=null){
  $limit=$r->user()->isOwner()?100:(int)Setting::valueOf('admin_discount_limit','10');
  $d=$r->validate(['name'=>'required|string|max:150','sku'=>['required','string','max:50',Rule::unique('products')->ignore($product?->id)],'category'=>'required|string|max:80|exists:product_categories,name','description'=>'required|string|max:5000','price'=>'required|integer|min:1|max:1000000000','discount'=>'required|integer|min:0|max:100','min_stock'=>'required|integer|min:0|max:1000000','image'=>[($product?->image?'nullable':'required'),'image','mimes:jpg,jpeg,png,webp','max:3072'],'image_left'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:3072','image_right'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:3072','active'=>'nullable|boolean'],['image.required'=>'Foto utama wajib diunggah.']);
  if((int)$d['discount']>$limit && (!$product || (int)$d['discount']!==(int)$product->discount))return back()->withInput()->withErrors(['discount'=>'Batas diskon Admin adalah '.$limit.'%.']);
  $d['active']=$r->boolean('active');
  foreach(['image','image_left','image_right'] as $field){
   unset($d[$field]);
   if($r->hasFile($field))$d[$field]=$r->file($field)->store('products','public');
  }
  DB::transaction(function()use($product,$d){
   // Share the category lock with deletion so concurrent users cannot save an orphan category.
   $category=ProductCategory::where('name',$d['category'])->lockForUpdate()->first();
   if(!$category)throw \Illuminate\Validation\ValidationException::withMessages(['category'=>'Kategori sudah tidak tersedia. Pilih kategori lain.']);
   $d['category']=$category->name;
   if($product)$product->update($d);else Product::create($d);
  });
  return redirect('/manage/products')->with('success','Produk berhasil disimpan.');
 }
 public function deactivate(Product $product){$product->update(['active'=>false]);return back()->with('success','Produk dinonaktifkan. Riwayat transaksi tetap tersimpan.');}
 public function costForm(Product $product){return view('admin.product-cost',['product'=>$product,'items'=>OrderItem::with('order')->where('product_id',$product->id)->whereHas('order',fn($q)=>$q->where('status','!=','cancelled'))->latest('id')->paginate(10),'revisions'=>DB::table('cost_revisions')->join('users','users.id','=','cost_revisions.user_id')->where('product_id',$product->id)->select('cost_revisions.*','users.name')->latest('cost_revisions.id')->paginate(10,["*"],"revision_page")->withQueryString()]);}
 public function saveCost(Request $r,Product $product){
  $d=$r->validate(['cost_price'=>'required|integer|min:0|max:1000000000','note'=>'required|string|max:500','items'=>'sometimes|array|max:20','items.*'=>'integer|distinct']);
  DB::transaction(function()use($product,$d,$r){
   $items=OrderItem::whereIn('id',$d['items']??[])->orderBy('id')->lockForUpdate()->get();
   if($items->count()!==count($d['items']??[]) || $items->contains(fn($i)=>(int)$i->product_id!==(int)$product->id))throw \Illuminate\Validation\ValidationException::withMessages(['items'=>'Transaksi yang dipilih tidak sesuai produk.']);
   $locked=Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
   $audit=['user_id'=>$r->user()->id,'product_id'=>$product->id,'new_cost'=>(int)$d['cost_price'],'note'=>$d['note'],'created_at'=>now(),'updated_at'=>now()];
   DB::table('cost_revisions')->insert($audit+['order_item_id'=>null,'old_cost'=>$locked->cost_price]);
   $locked->cost_price=(int)$d['cost_price'];
   $locked->cost_confirmed=true;
   $locked->save();
   foreach($items as $item){DB::table('cost_revisions')->insert($audit+['order_item_id'=>$item->id,'old_cost'=>$item->unit_cost]);$item->update(['unit_cost'=>(int)$d['cost_price'],'cost_confirmed'=>true]);}
  },3);
  return redirect('/manage/products')->with('success','Modal per unit berhasil diperbarui.');
 }
 public function stock(Request $r){
  $sort=in_array($r->query('sort'),['asc','desc'],true)?$r->query('sort'):null;
  $search=mb_substr(trim((string)$r->query('q','')),0,100);
  $matchingProduct=fn($query)=>$query->where(fn($q)=>$q->where('name','like','%'.$search.'%')->orWhere('sku','like','%'.$search.'%'));
  return view('admin.stock',[
   'sort'=>$sort,'search'=>$search,
   'products'=>Product::when($search!=='',$matchingProduct)->when($sort,fn($q)=>$q->orderBy('stock',$sort))->orderBy('name')->orderBy('id')->paginate(10,['*'],'stock_page')->withQueryString(),
   'movements'=>StockMovement::with('product','user')->when($search!=='',fn($q)=>$q->whereHas('product',$matchingProduct))->latest()->paginate(10,['*'],'movement_page')->withQueryString(),
  ]);
 }
 public function adjust(Request $r,InventoryService $inventory){
  $d=$r->validate(['product_id'=>'required|exists:products,id','quantity'=>'required|integer|not_in:0|min:-1000000|max:1000000','note'=>'required|string|max:500']);
  DB::transaction(function()use($d,$r,$inventory){$p=Product::whereKey($d['product_id'])->lockForUpdate()->firstOrFail();$inventory->move($p,(int)$d['quantity'],$r->user(),'adjustment','ADJ-'.now()->format('YmdHis'),$d['note']);},3);
  return back()->with('success','Penyesuaian stok dicatat.');
 }
 public function stockForm(Product $product){return view('admin.stock-form',['product'=>$product]);}
 public function saveStock(Request $r,Product $product,InventoryService $inventory){
  $d=$r->validate(['operation'=>'sometimes|required|in:add,subtract','addition'=>'required|integer|min:1|max:1000000','original_stock'=>'required|integer|min:0','note'=>'required|string|max:500']);
  DB::transaction(function()use($r,$product,$d,$inventory){
   $locked=Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
   if((int)$locked->stock!==(int)$d['original_stock'])throw \Illuminate\Validation\ValidationException::withMessages(['stock'=>'Stok telah berubah karena transaksi lain. Muat ulang halaman dan periksa jumlah terbaru sebelum menyimpan.']);
   $difference=(int)$d['addition']*(($d['operation']??'add')==='subtract'?-1:1);
   if($locked->stock+$difference>1000000)throw \Illuminate\Validation\ValidationException::withMessages(['addition'=>'Total stok maksimal 1.000.000 unit.']);
   if($difference!==0)$inventory->move($locked,$difference,$r->user(),'adjustment','ADJ-'.\Illuminate\Support\Str::uuid(),$d['note']);
  },3);
  return redirect('/manage/stock')->with('success','Stok berhasil disimpan. Perubahan jumlah dicatat dalam riwayat.');
 }
 public function orders(Request $r){
  $status=in_array($r->query('status'),['pending','processing','shipped','completed','cancelled'],true)?$r->query('status'):null;
  $search=is_string($r->query('q'))?mb_substr(trim($r->query('q')),0,100):'';
  $orders=Order::query()->when($status,fn($q)=>$q->where('status',$status))
   ->when($search!=='',fn($q)=>$q->where(function($match)use($search){
    $term='%'.$search.'%';
    $match->where('number','like',$term)->orWhere('customer_name','like',$term)
     ->orWhere('phone','like',$term)->orWhere('work_description','like',$term)
     ->orWhereHas('items',fn($items)=>$items->where('product_name','like',$term));
   }))->latest()->paginate(10)->withQueryString();
  return view('orders.index',['orders'=>$orders,'staff'=>true,'search'=>$search,'status'=>$status]);
 }
 public function transition(Request $r,Order $order,InventoryService $inventory){$d=$r->validate(['status'=>'required|in:processing,shipped,completed,cancelled']);$inventory->transition($order,$d['status'],$r->user());return back()->with('success','Status pesanan diperbarui.');}
 public function saleForm(){return view('admin.sale',['products'=>Product::where('active',true)->whereColumn('stock','>','reserved_stock')->get(),'token'=>(string)\Illuminate\Support\Str::uuid()]);}
 public function reports(Request $r){
  $r->validate(['q'=>'nullable|string|max:150']);
  $search=trim($r->input('q',''));
  $matchOrder=function($q)use($search){$q->where('number','like','%'.$search.'%')->orWhere('customer_name','like','%'.$search.'%')->orWhere('work_description','like','%'.$search.'%');};
  if(!$r->user()->isOwner()){
   abort_if($r->boolean('export'),403);
   $query=\App\Models\Payment::today();
   if($search!=='')$query->whereHas('order',$matchOrder);
   return view('admin.daily-income',['revenue'=>(clone $query)->sum('amount'),'incoming'=>(clone $query)->where('amount','>',0)->sum('amount'),'refunds'=>-(clone $query)->where('amount','<',0)->sum('amount'),'cash'=>(clone $query)->where('method','cash')->sum('amount'),'transfer'=>(clone $query)->where('method','transfer')->sum('amount'),'orders'=>$query->with('order')->latest('received_at')->paginate(10)->withQueryString()]);
  }
  $d=$r->validate(['from'=>'nullable|date_format:Y-m-d','to'=>'nullable|date_format:Y-m-d|after_or_equal:from']);
  $from=$d['from']??now()->startOfMonth()->toDateString();$to=$d['to']??now()->toDateString();
  $query=Order::where('status','completed')->whereBetween('completed_at',[$from.' 00:00:00',$to.' 23:59:59']);
  $payments=\App\Models\Payment::whereBetween('received_at',[$from.' 00:00:00',$to.' 23:59:59']);
  $revenue=(clone $query)->sum('total');$items=OrderItem::whereIn('order_id',(clone $query)->select('id'));
  $data=['expenses'=>\App\Models\Expense::whereDate('spent_on','>=',$from)->whereDate('spent_on','<=',$to)->sum('amount'),'incoming'=>(clone $payments)->where('amount','>',0)->sum('amount'),'refunds'=>-(clone $payments)->where('amount','<',0)->sum('amount'),'receivables'=>Order::where('staged_sale',true)->where('status','!=','cancelled')->get()->sum(fn($o)=>$o->balance),'from'=>$from,'to'=>$to,'revenue'=>$revenue,'productRevenue'=>(clone $items)->where('item_type','product')->sum(DB::raw('quantity * unit_price')),'serviceRevenue'=>(clone $items)->where('item_type','service')->sum(DB::raw('quantity * unit_price')),'quantity'=>(clone $items)->where('item_type','product')->sum('quantity'),'orders'=>(clone $query)->latest('completed_at')->paginate(10)->withQueryString(),'stock'=>Product::sum('stock'),'count'=>(clone $query)->count()];
  if($r->user()->isOwner()){$data['missingCost']=(clone $items)->where('cost_confirmed',false)->count();$data['cost']=(clone $items)->sum(DB::raw('quantity * unit_cost'));$data['profit']=$revenue-$data['cost'];$data['stockValue']=Product::sum(DB::raw('stock * cost_price'));}
  if($search!==''){$query->where($matchOrder);$data['orders']=(clone $query)->latest('completed_at')->paginate(10)->withQueryString();}
  if($r->boolean('export')){
   $owner=$r->user()->isOwner();
   return response()->streamDownload(function()use($query,$owner){$out=fopen('php://output','w');fputcsv($out,$owner?['Nomor','Tanggal selesai','Omzet','Modal','Laba kotor','Status modal']:['Nomor','Tanggal selesai','Omzet'],',','"','');foreach($query->with('items')->cursor() as $o){$row=[$o->number,$o->completed_at->toDateString(),$o->total];if($owner){$cost=$o->items->sum(fn($i)=>$i->quantity*$i->unit_cost);$row[]=$cost;$row[]=$o->total-$cost;$row[]=$o->items->contains(fn($i)=>!$i->cost_confirmed)?'Modal belum lengkap - laba sementara':'Lengkap';}fputcsv($out,$row,',','"','');}fclose($out);},'laporan-penjualan.csv',['Content-Type'=>'text/csv; charset=UTF-8']);
  }
  return view('admin.reports',$data);
 }
 public function settings(){return view('admin.settings',['limit'=>Setting::valueOf('admin_discount_limit','10')]);}
 public function saveSettings(Request $r){$d=$r->validate(['limit'=>'required|integer|min:0|max:100']);Setting::updateOrCreate(['key'=>'admin_discount_limit'],['value'=>$d['limit']]);return back()->with('success','Batas diskon disimpan.');}
}


