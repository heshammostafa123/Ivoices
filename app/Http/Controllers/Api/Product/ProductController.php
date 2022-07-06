<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['sections'] = Section::all();
        $data['products'] = Product::all();
        return $this->apiResponse($data, 'ok', 200);
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
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|max:255|unique:products',
                'section_id'=>'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $Product = Product::create([
                'product_name' => $request->product_name,
                'section_id' => $request->section_id,
                'description' => $request->description,
            ]);
            if ($Product) {
                return $this->apiResponse($Product, "Product saved", 201);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'max:255|unique:products,product_name,' . $request->id,
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $Product = Product::find($id);
            if (!$Product) {
                return $this->apiResponse(null, "the Product not found", 404);
            }
            $Product->update($request->all());
            if ($Product) {
                return $this->apiResponse($Product, "Product updated", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $Product = Product::find($id);
            if (!$Product) {
                return $this->apiResponse(null, "the Product not found", 404);
            }
            $Product->delete($id);
            if ($Product) {
                return $this->apiResponse(null, "Product Deleted", 200);
            }
        }catch (\Exception $th) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }
}
