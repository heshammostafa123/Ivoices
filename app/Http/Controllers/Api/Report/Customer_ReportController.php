<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Customer_ReportController extends Controller
{
    use ApiResponseTrait;
    public function Search_customers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required',
            'product' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }
      // في حالة البحث بدون التاريخ
  
      if ($request->section_id && $request->product && $request->start_at == '' && $request->end_at == '') {
        $data['invoices'] = Invoice::select('*')->where('section_id', '=', $request->section_id)->where('product', '=', $request->product)->get();
        $data['sections'] = Section::all();
        if ($data['invoices']) {
            return $this->apiResponse($data, "invoices Data", 200);
        }
        return $this->apiResponse(null, "the invoices not found", 404);
      }
      // في حالة البحث بتاريخ
  
      else {
  
        $start_at = date($request->start_at);
        $end_at = date($request->end_at);
  
        $data['invoices'] = Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('section_id', '=', $request->section_id)->where('product', '=', $request->product)->get();
        $data['sections'] = Section::all();
        if ($data['invoices']) {
            return $this->apiResponse($data, "invoices Data", 200);
        }
        return $this->apiResponse(null, "the invoices not found", 404);
      }
    }
}
