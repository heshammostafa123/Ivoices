<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Invoice_ReportController extends Controller
{
    use ApiResponseTrait;
    public function Search_invoices(Request $request)
    {
        

        try {
            $validator = Validator::make($request->all(), [
                'rdio' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $rdio = $request->rdio;


            // في حالة البحث بنوع الفاتورة

            if ($rdio == 1) {

                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                ]);
                if ($validator->fails()) {
                    return $this->apiResponse(null, $validator->errors(), 400);
                }
                // في حالة عدم تحديد تاريخ
                if ($request->type && $request->start_at == '' && $request->end_at == '') {

                    $invoices = Invoice::select('*')->where('Status', '=', $request->type)->get();
                    if ($invoices) {
                        return $this->apiResponse($invoices, "invoices Data", 200);
                    }
                    return $this->apiResponse(null, "the invoices not found", 404);
                }

                // في حالة تحديد تاريخ استحقاق
                else {

                    $start_at = date($request->start_at);
                    $end_at = date($request->end_at);
                    $type = $request->type;

                    $invoices = Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('Status', '=', $request->type)->get();
                    if ($invoices) {
                        return $this->apiResponse($invoices, "invoices Data", 200);
                    }
                    return $this->apiResponse(null, "the invoices not found", 404);
                }
            }

            //====================================================================

            // في البحث برقم الفاتورة
            else {
                $validator = Validator::make($request->all(), [
                    'invoice_number' => 'required',
                ]);
                if ($validator->fails()) {
                    return $this->apiResponse(null, $validator->errors(), 400);
                }
                $invoices = Invoice::select('*')->where('invoice_number', '=', $request->invoice_number)->get();
                if ($invoices) {
                    return $this->apiResponse($invoices, "invoices Data", 200);
                }
                return $this->apiResponse(null, "the invoices not found", 404);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    
    }
}
