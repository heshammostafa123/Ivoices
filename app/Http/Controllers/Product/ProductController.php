<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
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
        return view('products.products', ['products' => $products, 'sections' => $sections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            Product::create([
                'product_name' => $request->Product_name,
                'section_id' => $request->section_id,
                'description' => $request->description,
            ]);
            session()->flash('Add', 'تم اضافة المنتج بنجاح ');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request)
    {
        try {
            $id = Section::where('section_name', $request->section_name)->first()->id;

            $Products = Product::findOrFail($request->pro_id);

            $Products->update([
                'product_name' => $request->Product_name,
                'description' => $request->description,
                'section_id' => $id,
            ]);

            session()->flash('Edit', 'تم تعديل المنتج بنجاح');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
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
            return back();
        }catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }
}
