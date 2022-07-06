<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceAchiveController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $invoices = Invoice::onlyTrashed()->get();
            return $this->apiResponse($invoices, 'Archived Invoices Data', 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
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
        //
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
    public function update(Request $request,$id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'invoice_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $id = $request->invoice_id;
            $flight = Invoice::withTrashed()->where('id', $id)->restore();
            if ($flight) {
                return $this->apiResponse($flight, "Invoice Restored", 200);
            }
            return $this->apiResponse(null, "the Invoice not found", 404);
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
            $invoice = Invoice::withTrashed()->where('id',$id)->first();
            if (!$invoice) {
                return $this->apiResponse(null, "the Invoice not found", 404);
            }
            $invoice->forceDelete();
            if ($invoice) {
                return $this->apiResponse(null, "Invoice Deleted", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }

    }
}
