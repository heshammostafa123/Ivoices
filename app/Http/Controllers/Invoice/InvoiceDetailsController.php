<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Invoice_attachments;
use App\Models\Invoice_details;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;

class InvoiceDetailsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = Invoice::where('id',$id)->first();
        $details  = InvoiceDetails::where('invoice_id',$id)->get();
        $attachments  = InvoiceAttachment::where('invoice_id',$id)->get();
        return view('invoices.invoice_details',compact('invoices','details','attachments'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoices = InvoiceAttachment::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

     public function getFile($invoice_number,$file_name)
    {
        $contents= Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->download( $contents);
    }



    public function openFile($invoice_number,$file_name)
    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->file($files);
    }

}