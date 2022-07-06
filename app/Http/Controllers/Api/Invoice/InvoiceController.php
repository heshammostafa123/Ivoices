<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Invoice;
use App\Models\Invoice_attachments;
use App\Models\Invoice_details;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::all();
        return $this->apiResponse($invoices, 'ok', 200);
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
                'invoice_number' =>  'required|unique:invoices',
                'invoice_Date' =>    'required',
                'Due_date' =>    'required',
                'product' =>    'required',
                'section_id' =>    'required',
                'Amount_collection' =>    'required',
                'Amount_Commission' =>    'required',
                'Value_VAT' =>    'required',
                'Rate_VAT' =>    'required',
                'Total' =>    'required',
                'pic' => 'mimes:jpg,jpeg,png,pdf',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            DB::beginTransaction();
            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'invoice_Date' => $request->invoice_Date,
                'Due_date' => $request->Due_date,
                'product' => $request->product,
                'section_id' => $request->section_id,
                'Amount_collection' => $request->Amount_collection,
                'Amount_Commission' => $request->Amount_Commission,
                'Discount' => $request->Discount,
                'Value_VAT' => $request->Value_VAT,
                'Rate_VAT' => $request->Rate_VAT,
                'Total' => $request->Total,
                'Status' => 'غير مدفوعة',
                'Value_Status' => 2,
                'note' => $request->note,
            ]);

            $invoice_id = Invoice::latest()->first()->id;
            Invoice_details::create([
                'id_Invoice' => $invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->section_id,
                'Status' => 'غير مدفوعة',
                'Value_Status' => 2,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]);

            if ($request->hasFile('pic')) {

                //$invoice_id = Invoices::latest()->first()->id;
                $image = $request->file('pic');
                $file_name = $image->getClientOriginalName();
                $invoice_number = $request->invoice_number;

                $attachments = new Invoice_attachments();
                $attachments->file_name = $file_name;
                $attachments->invoice_number = $invoice_number;
                $attachments->Created_by = Auth::user()->name;
                $attachments->invoice_id = $invoice_id;
                $attachments->save();

                // move pic
                $imageName = $request->pic->getClientOriginalName();
                $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
            }


            $user = User::first();
            Notification::send($user, new AddInvoice($invoice_id));

            // //$user = User::get();
            // //send mail to users with specific permissions
            // $user=User::permission('الاشعارات')->get();
            // $invoices = Invoice::latest()->first();
            // Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));
            DB::commit();
            if ($invoice) {
                return $this->apiResponse($invoice, "Invoice saved", 201);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->apiResponse(null, $exception->getMessage(), 400);
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
        try {
            $Invoice = Invoice::find($id);
            if ($Invoice) {
                return $this->apiResponse($Invoice, "Invoice Data", 200);
            }
            return $this->apiResponse(null, "the Invoice not found", 404);
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
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->apiResponse(null, "the invoice not found", 404);
            }
            $invoice->update($request->all());
            if ($invoice) {
                return $this->apiResponse($invoice, "invoice updated", 200);
            }
        } catch (\Exception $th) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
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
            $id = $request->invoice_id;
            $invoices = Invoice::where('id', $id)->first();
            if (!$invoices) {
                return $this->apiResponse(null, "the Invoice not found", 404);
            }
            $Details = invoice_attachments::where('invoice_id', $id)->first(); ////to know the directory of attachments

            $id_page = $request->id_page;


            if (!$id_page == 2) {
                ///delete
                if (!empty($Details->invoice_number)) {

                    Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
                }

                $invoices->forceDelete();
                if ($invoices) {
                    return $this->apiResponse(null, "Invoice Deleted", 200);
                }
            } else {
                //archive
                $invoices->delete();
                if ($invoices) {
                    return $this->apiResponse(null, "Invoice Archived", 200);
                }
            }
        } catch (\Exception $th) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function getproducts($id)
    {
        try {
            $section = Section::find($id);
            if (!$section) {
                return $this->apiResponse(null, "the Section not found", 404);
            }
            $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
            if ($products) {
                return $this->apiResponse($products, "Products Data", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function Status_Update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Status' => 'required',
                'Payment_Date' => 'required',
                'invoice_id' => 'required',
                'invoice_number' => 'required',
                'product' => 'required',
                'Section' => 'required',
                'note' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors(), 400);
            }
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->apiResponse(null, "the invoice not found", 404);
            }
            if ($request->Status === 'مدفوعة') {
                DB::beginTransaction();
                $invoice->update([
                    'Value_Status' => 1,
                    'Status' => $request->Status,
                    'Payment_Date' => $request->Payment_Date,
                ]);

                Invoice_details::create([
                    'id_Invoice' => $request->invoice_id,
                    'invoice_number' => $request->invoice_number,
                    'product' => $request->product,
                    'Section' => $request->Section,
                    'Status' => $request->Status,
                    'Value_Status' => 1,
                    'note' => $request->note,
                    'Payment_Date' => $request->Payment_Date,
                    'user' => (Auth::user()->name),
                ]);
                DB::commit();
            } else {
                DB::beginTransaction();
                $invoice->update([
                    'Value_Status' => 3,
                    'Status' => $request->Status,
                    'Payment_Date' => $request->Payment_Date,
                ]);
                Invoice_details::create([
                    'id_Invoice' => $request->invoice_id,
                    'invoice_number' => $request->invoice_number,
                    'product' => $request->product,
                    'Section' => $request->Section,
                    'Status' => $request->Status,
                    'Value_Status' => 3,
                    'note' => $request->note,
                    'Payment_Date' => $request->Payment_Date,
                    'user' => (Auth::user()->name),
                ]);
                DB::commit();
            }
            if ($invoice) {
                return $this->apiResponse($invoice, "invoice updated", 200);
            }
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function Print_invoice($id)
    {
        try {
            $invoice = Invoice::where('id', $id)->first();
            if ($invoice) {
                return $this->apiResponse($invoice, "Invoice Data", 200);
            }
            return $this->apiResponse(null, "the Invoice not found", 404);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function Invoice_Paid()
    {
        try {
            $invoices = Invoice::where('Value_Status', 1)->get();
            return $this->apiResponse($invoices, "Invoices Data", 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function Invoice_unPaid()
    {
        try {
            $invoices = Invoice::where('Value_Status', 2)->get();
            return $this->apiResponse($invoices, "Invoices Data", 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }
    
    public function Invoice_Partial()
    {
        try {
            $invoices = Invoice::where('Value_Status', 3)->get();
            return $this->apiResponse($invoices, "Invoices Data", 200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null, "Invalid data", 400);
        }
    }

    public function MarkAsRead_all(Request $request)
    {

        $userUnreadNotification = auth()->user()->unreadNotifications;

        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return $this->apiResponse(null, "All Notification Marked As Read", 200);
        }
    }
}
