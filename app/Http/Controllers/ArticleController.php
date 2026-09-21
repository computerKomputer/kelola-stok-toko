<?php
namespace App\Http\Controllers;
use App\Models\{Article,Category};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ArticleController {
 private function access(Request $r,Article $article):void {abort_unless($r->user()->isStaff() || $article->user_id===$r->user()->id,403);}
 public function index(Request $r){return view('admin.articles',['articles'=>Article::with('author')->when($r->user()->role==='writer',fn($q)=>$q->where('user_id',$r->user()->id))->latest()->paginate(10)]);}
 public function form(Request $r,?Article $article=null){if($article)$this->access($r,$article);return view('admin.article-form',['article'=>$article??new Article(),'categories'=>Category::orderBy('name')->get()]);}
 public function save(Request $r,?Article $article=null){
  if($article)$this->access($r,$article);
  $d=$r->validate(['title'=>'required|string|max:180','category'=>'required|exists:categories,name','tags'=>'nullable|string|max:200','excerpt'=>'required|string|max:350','body'=>'required|string|max:50000','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:3072']);
  unset($d['image']);if($r->hasFile('image'))$d['image']=$r->file('image')->store('articles','public');
  if($article)$article->update($d);else{$d['user_id']=$r->user()->id;$d['slug']=Str::slug($d['title']).'-'.Str::lower(Str::random(6));Article::create($d);}
  return redirect('/manage/articles')->with('success','Berita sudah dipublikasikan.');
 }
 public function delete(Request $r,Article $article){abort_unless($r->user()->isStaff(),403);$article->delete();return back()->with('success','Berita dihapus.');}
 public function categories(){return view('admin.categories',['categories'=>Category::orderBy('name')->paginate(10)]);}
 public function saveCategory(Request $r){$d=$r->validate(['name'=>'required|string|max:80|unique:categories']);Category::create($d);return back()->with('success','Kategori ditambahkan.');}
 public function deleteCategory(Category $category){if(Article::where('category',$category->name)->exists())return back()->withErrors(['category'=>'Kategori masih dipakai oleh berita.']);$category->delete();return back()->with('success','Kategori dihapus.');}
}
