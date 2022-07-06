<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view('products.products', compact('products','sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            Product::create(request()->all());
            session()->flash('Add', 'تم اضافة المنتج بنجاح ');
            return redirect()->route('products.index');
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->route('products.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request)
    {
        try {
            $id = Section::where('section_name', $request->section_name)->first()->id;
            $Products = Product::findOrFail($request->pro_id);
            $product=$request->merge(['section_id'=>$id])->toArray();
            $Products->update($product);
            session()->flash('Edit', 'تم تعديل المنتج بنجاح');
            return redirect()->route('products.index');
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->route('products.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $Products = Product::findOrFail($request->pro_id);
            $Products->delete();
            session()->flash('delete', 'تم حذف المنتج بنجاح');
            return redirect()->route('products.index');
        }catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->route('products.index');
        }
    }
}
