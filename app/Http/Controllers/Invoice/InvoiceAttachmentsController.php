<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceAttachmnetRequest;
use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceAttachmnetRequest $request)
    {
        $image = $request->file('file_name');
        $file_name = $image->getClientOriginalName();

        $attachments =  new InvoiceAttachment();
        $attachments->file_name = $file_name;
        $attachments->invoice_number = $request->invoice_number;
        $attachments->invoice_id = $request->invoice_id;
        $attachments->created_by = Auth::user()->name;
        $attachments->save();
            
        // move pic
        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachments/'. $request->invoice_number), $imageName);
        
        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return back();
    }

}
