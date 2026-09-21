<?php
namespace App\Http\Controllers;
use App\Models\{Product,Article,Order,User};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class StoreController {
 public function home(){return view('store.home',['categories'=>Product::where('active',true)->distinct()->orderBy('category')->pluck('category'),'products'=>Product::where('active',true)->orderBy('id')->take(8)->get(),'articles'=>Article::with('author')->latest()->take(3)->get()]);}
 public function products(Request $r){
  $q=Product::where('active',true)->when($r->q,fn($q)=>$q->where('name','like','%'.mb_substr($r->q,0,100).'%'))->when($r->category,fn($q)=>$q->where('category',$r->category));
  return view('store.products',['products'=>$q->latest()->paginate(16)->withQueryString(),'categories'=>Product::where('active',true)->distinct()->pluck('category')]);
 }
 public function product(Product $product){abort_unless($product->active,404);return view('store.product',compact('product'));}
 public function articles(Request $r){return view('store.articles',['articles'=>Article::with('author')->when($r->q,fn($q)=>$q->where('title','like','%'.mb_substr($r->q,0,100).'%'))->when($r->category,fn($q)=>$q->where('category',$r->category))->latest()->paginate(12)->withQueryString(),'categories'=>Article::distinct()->pluck('category')]);}
 public function article(Article $article){$article->increment('views');return view('store.article',compact('article'));}
 public function author(User $user){abort_unless(in_array($user->role,['writer','admin','owner']),404);return view('store.author',['author'=>$user,'articles'=>Article::where('user_id',$user->id)->latest()->paginate(10)]);}
 public function cart(Request $r){
  $cart=$r->session()->get('cart',[]);$products=Product::whereIn('id',array_keys($cart))->get();$total=$products->sum(fn($p)=>$p->sale_price*($cart[$p->id]??0));
  if(!$r->session()->has('checkout_token'))$r->session()->put('checkout_token',(string)Str::uuid());
  return view('store.cart',compact('cart','products','total'));
 }
 public function add(Request $r,Product $product){
  $d=$r->validate(['quantity'=>'required|integer|min:1|max:1000']);abort_unless($product->active,404);
  $cart=$r->session()->get('cart',[]);$qty=($cart[$product->id]??0)+$d['quantity'];
  if($qty>$product->available_stock)return back()->withErrors(['quantity'=>'Jumlah melebihi stok tersedia.']);
  $cart[$product->id]=$qty;$r->session()->put('cart',$cart);$r->session()->forget('checkout_token');return back()->with('success','Produk ditambahkan ke keranjang.');
 }
 public function updateCart(Request $r,Product $product){
  $d=$r->validate(['quantity'=>'required|integer|min:0|max:1000']);$cart=$r->session()->get('cart',[]);
  if($d['quantity']>$product->available_stock)return back()->withErrors(['quantity'=>'Jumlah melebihi stok tersedia.']);
  if($d['quantity']===0 || $d['quantity']==='0')unset($cart[$product->id]);else $cart[$product->id]=(int)$d['quantity'];
  $r->session()->put('cart',$cart);$r->session()->forget('checkout_token');return back()->with('success','Keranjang diperbarui.');
 }
 public function checkout(Request $r,InventoryService $inventory){
  $d=$r->validate(['customer_name'=>'required|string|max:100','phone'=>'required|string|max:30','address'=>'required|string|max:1000','checkout_token'=>'required|uuid']);
  abort_unless(hash_equals((string)$r->session()->get('checkout_token',''),$d['checkout_token']),419);
  $order=$inventory->checkout($r->user(),$r->session()->get('cart',[]),$d);
  $r->session()->forget('cart');return redirect('/orders/'.$order->id)->with('success','Pesanan dibuat. Pembayaran dilakukan saat barang diterima (COD).');
 }
 public function orders(Request $r){return view('orders.index',['orders'=>Order::where('user_id',$r->user()->id)->latest()->paginate(10),'staff'=>false]);}
 public function order(Request $r,Order $order){abort_unless($r->user()->isStaff() || $order->user_id===$r->user()->id,403);$order->load('items');return view('orders.show',compact('order'));}
}

