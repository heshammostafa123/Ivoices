<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Invoice_attachments;
use App\Models\Invoice_details;
<<<<<<< HEAD
<<<<<<< HEAD
use App\Models\InvoiceAttachment;
use App\Models\InvoiceDetails;
=======
use Exception;
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
>>>>>>> parent of a03dbdd... api commit
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;

class InvoiceDetailsController extends Controller
{
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
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
<<<<<<< HEAD
        $invoices = Invoice::where('id',$id)->first();
<<<<<<< HEAD
        $details  = InvoiceDetails::where('invoice_id',$id)->get();
        $attachments  = InvoiceAttachment::where('invoice_id',$id)->get();
        return view('invoices.invoice_details',compact('invoices','details','attachments'));
=======
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                session()->flash('error', 'الفاتوره غير موجوده');
                return back();
            }

            $invoices = Invoice::where('id', $id)->first();
            $details  = Invoice_details::where('id_Invoice', $id)->get();
            $attachments  = Invoice_attachments::where('invoice_id', $id)->get();
            return view('invoices.details_invoice', compact('invoices', 'details', 'attachments'));
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
        $details  = Invoice_details::where('id_Invoice',$id)->get();
        $attachments  = Invoice_attachments::where('invoice_id',$id)->get();
        return view('invoices.details_invoice',compact('invoices','details','attachments'));
>>>>>>> parent of a03dbdd... api commit
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $invoices = InvoiceAttachment::findOrFail($request->id_file);
=======
        $invoices = invoice_attachments::findOrFail($request->id_file);
>>>>>>> parent of a03dbdd... api commit
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

<<<<<<< HEAD
     public function getFile($invoice_number,$file_name)
=======
        try {
            $invoices = invoice_attachments::findOrFail($request->id_file);
            $invoices->delete();
            Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
            session()->flash('delete', 'تم حذف المرفق بنجاح');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }

    public function get_file($invoice_number, $file_name)
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
     public function get_file($invoice_number,$file_name)

>>>>>>> parent of a03dbdd... api commit
    {
        try{
            $contents = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
            return response()->download($contents);
        }catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }



<<<<<<< HEAD
<<<<<<< HEAD
    public function openFile($invoice_number,$file_name)
=======
    public function open_file($invoice_number, $file_name)
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
    public function open_file($invoice_number,$file_name)
>>>>>>> parent of a03dbdd... api commit
    {
        try{
            $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
            return response()->file($files);
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }
}
