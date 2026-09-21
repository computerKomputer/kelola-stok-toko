<?php
namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductCategoryController
{
    public function index()
    {
        return view('admin.product-categories', [
            'categories' => ProductCategory::withCount('products')->orderBy('name')->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:80|unique:product_categories,name']);
        // Concurrent duplicate submissions are also protected by the unique index.
        $category = ProductCategory::firstOrCreate(['name' => $data['name']]);
        if (!$category->wasRecentlyCreated) {
            throw ValidationException::withMessages(['name' => 'Kategori produk sudah tersedia.']);
        }
        return redirect('/manage/product-categories')->with('success', 'Kategori produk ditambahkan. Sekarang kategori ini dapat dipilih pada formulir produk.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        DB::transaction(function () use ($productCategory) {
            $category = ProductCategory::whereKey($productCategory->id)->lockForUpdate()->firstOrFail();
            if ($category->products()->exists()) {
                throw ValidationException::withMessages(['category' => 'Kategori masih digunakan oleh produk, termasuk produk nonaktif. Ubah kategori produk tersebut terlebih dahulu.']);
            }
            $category->delete();
        });
        return back()->with('success', 'Kategori produk dihapus.');
    }
}
