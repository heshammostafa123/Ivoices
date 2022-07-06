<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Invoice;
use App\Models\Invoice_attachments;
use App\Models\Invoice_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvoiceDetailsController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
        $data['invoice'] = Invoice::where('id',$id)->first();
        if (!$data['invoice']) {
            return $this->apiResponse(null, "the invoice not found", 404);
        }
        $data['details'] = Invoice_details::where('id_Invoice',$id)->get();
        $data['attachments']  = Invoice_attachments::where('invoice_id',$id)->get();
        return $this->apiResponse($data, "Invoice Data", 200);

    } catch (\Exception $exception) {
        return $this->apiResponse(null, "Invalid data", 400);
    }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_file' => 'required',
                'invoice_number' => 'required',
                'file_name' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $invoice = invoice_attachments::find($request->id_file);
            if (!$invoice) {
                return $this->apiResponse(null, "The Invoice Attachment not found", 404);
            }
            $invoice->delete($request->id_file);
            Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
            if ($invoice) {
                return $this->apiResponse(null, "Invoice Attachment Deleted", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function get_file($invoice_number,$file_name)
    {
        $contents= Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        if (!$contents) {
            return $this->apiResponse(null, "The Invoice Attachment not found", 404);
        }
        return response()->download( $contents);
    }

    public function open_file($invoice_number,$file_name)
    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        if (!$files) {
            return $this->apiResponse(null, "The Invoice Attachment not found", 404);
        }
        return response()->file($files);
    }
}
