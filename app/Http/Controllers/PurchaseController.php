<?php
namespace App\Http\Controllers;
use App\Models\{Purchase,Supplier,Product};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PurchaseController {
 public function index(){return view('admin.purchases',['purchases'=>Purchase::with('items.product')->latest()->paginate(10)]);}
 public function create(){return view('admin.purchase-form',['suppliers'=>Supplier::orderBy('name')->get(),'products'=>Product::orderBy('name')->get()]);}
 public function store(Request $r){
  $d=$r->validate(['supplier_id'=>'required|exists:suppliers,id','product_id'=>'required|exists:products,id','quantity'=>'required|integer|min:1|max:1000000','unit_cost'=>'required|integer|min:0|max:1000000000','extra_cost'=>'required|integer|min:0|max:1000000000','note'=>'nullable|string|max:500']);
  DB::transaction(function()use($d){$p=Purchase::create(['number'=>'PO-'.strtoupper(bin2hex(random_bytes(5))),'supplier_id'=>$d['supplier_id'],'extra_cost'=>$d['extra_cost'],'note'=>$d['note']??null]);$p->items()->create(['product_id'=>$d['product_id'],'quantity'=>$d['quantity'],'unit_cost'=>$d['unit_cost']]);});
  return redirect('/manage/purchases')->with('success','Pembelian dibuat. Stok bertambah setelah barang diterima.');
 }
 public function receive(Request $r,Purchase $purchase,InventoryService $inventory){$d=$r->validate(['receipt_token'=>'required|uuid','quantities'=>'required|array','quantities.*'=>'nullable|integer|min:0|max:1000000']);$inventory->receive($purchase,$d['quantities'],$r->user(),$d['receipt_token']);return back()->with('success','Penerimaan barang berhasil dicatat.');}
 public function suppliers(){return view('admin.suppliers',['suppliers'=>Supplier::orderBy('name')->paginate(10)]);}
 public function saveSupplier(Request $r,?Supplier $supplier=null){$d=$r->validate(['name'=>'required|string|max:150','phone'=>'required|string|max:30','address'=>'nullable|string|max:500']);if($supplier)$supplier->update($d);else Supplier::create($d);return back()->with('success','Supplier disimpan.');}
 public function deleteSupplier(Supplier $supplier){if(Purchase::where('supplier_id',$supplier->id)->exists())return back()->withErrors(['supplier'=>'Supplier memiliki riwayat pembelian dan tidak bisa dihapus.']);$supplier->delete();return back()->with('success','Supplier dihapus.');}
}
