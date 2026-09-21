<?php
namespace App\Http\Controllers;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class ExpenseCategoryController {
 public function index(){return view('admin.expense-categories',['categories'=>ExpenseCategory::withCount('expenses')->orderBy('id')->paginate(10)]);}
 public function store(Request $r){
  $d=$r->validate(['name'=>['required','string','max:100',Rule::notIn(['Pengembalian DP']),Rule::unique('expense_categories','name')]]);
  ExpenseCategory::create(['code'=>'custom-'.Str::uuid(),'name'=>$d['name'],'active'=>true]);
  return back()->with('success','Kategori pengeluaran ditambahkan.');
 }
 public function update(Request $r,ExpenseCategory $expenseCategory){
  $d=$r->validate(['name'=>['required','string','max:100',Rule::notIn(['Pengembalian DP']),Rule::unique('expense_categories','name')->ignore($expenseCategory->id)],'active'=>'required|boolean']);
  $expenseCategory->update($d);
  return back()->with('success','Kategori diperbarui. Riwayat pengeluaran tetap tersimpan.');
 }
}
