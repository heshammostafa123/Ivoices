<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Notifications\AddInvoice;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\MyEventClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Invoice_attachments;
use App\Models\Invoice_details;
use App\Models\Section;
use App\Models\User;
use Exception;
use PhpParser\Node\Stmt\TryCatch;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        try {
            Invoice::create([
                'invoice_number' => $request->invoice_number,
                'invoice_Date' => $request->invoice_Date,
                'Due_date' => $request->Due_date,
                'product' => $request->product,
                'section_id' => $request->Section,
                'Amount_collection' => $request->Amount_collection,
                'Amount_Commission' => $request->Amount_Commission,
                'Discount' => $request->Discount,
                'Value_VAT' => $request->Value_VAT,
                'Rate_VAT' => $request->Rate_VAT,
                'Total' => $request->Total,
                'Status' => '?????? ????????????',
                'Value_Status' => 2,
                'note' => $request->note,
            ]);

            $invoice_id = Invoice::latest()->first()->id;
            Invoice_details::create([
                'id_Invoice' => $invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => '?????? ????????????',
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

            //$user = User::get();
            //send mail to users with specific permissions
            $user = User::permission('??????????????????')->get();
            $invoices = Invoice::latest()->first();
            Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));

            session()->flash('Add', '???? ?????????? ???????????????? ??????????');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                session()->flash('error', '???????????????? ?????? ????????????');
                return redirect('invoices');
            }
            $invoices = Invoice::where('id', $id)->first();
            return view('invoices.status_update', compact('invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
<<<<<<< HEAD
        $invoices = Invoice::where('id', $id)->first();
        $sections = Section::all();
<<<<<<< HEAD
        return view('invoices.edit', compact('sections', 'invoices'));
=======
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                session()->flash('error', '???????????????? ?????? ????????????');
                return redirect('invoices');
            }
            $invoices = Invoice::where('id', $id)->first();
            $sections = Section::all();
            return view('invoices.edit_invoice', compact('sections', 'invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
>>>>>>> parent of a03dbdd... api commit
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request)
    {
        try {
            $invoices = Invoice::findOrFail($request->invoice_id);
            $invoices->update([
                'invoice_number' => $request->invoice_number,
                'invoice_Date' => $request->invoice_Date,
                'Due_date' => $request->Due_date,
                'product' => $request->product,
                'section_id' => $request->Section,
                'Amount_collection' => $request->Amount_collection,
                'Amount_Commission' => $request->Amount_Commission,
                'Discount' => $request->Discount,
                'Value_VAT' => $request->Value_VAT,
                'Rate_VAT' => $request->Rate_VAT,
                'Total' => $request->Total,
                'note' => $request->note,
            ]);

            session()->flash('edit', '???? ?????????? ???????????????? ??????????');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->invoice_id;
            $invoices = Invoice::where('id', $id)->first();
            $Details = invoice_attachments::where('invoice_id', $id)->first(); ////to know the directory of attachments

            $id_page = $request->id_page;


            if (!$id_page == 2) {
                ///delete
                if (!empty($Details->invoice_number)) {

                    Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
                }

                $invoices->forceDelete();
                session()->flash('delete_invoice');
                return redirect('/invoices');
            } else {
                //archive
                $invoices->delete();
                session()->flash('archive_invoice');
                return redirect('/Archive');
            }
        } catch (\Exception $th) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function Status_Update($id, Request $request)
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
        
>>>>>>> parent of a03dbdd... api commit
        $invoices = Invoice::findOrFail($id);

        if ($request->Status === '????????????') {

            $invoices->update([
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
        } else {
            $invoices->update([
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
        }
        session()->flash('Status_Update');
<<<<<<< HEAD
        return redirect()->route('invoices.index');
=======
        try {
            $invoices = Invoice::findOrFail($id);

            if ($request->Status === '????????????') {

                $invoices->update([
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
            } else {
                $invoices->update([
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
            }
            session()->flash('Status_Update');
            return redirect('/invoices');
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
        return redirect('/invoices');
>>>>>>> parent of a03dbdd... api commit
    }

    public function Print_invoice($id)
    {
<<<<<<< HEAD
        $invoices = Invoice::where('id', $id)->first();
<<<<<<< HEAD
        return view('invoices.print_invoice', compact('invoices'));
=======
        try {
            $invoices = Invoice::find($id);
            if (!$invoices) {
                session()->flash('error', '???????????????? ?????? ????????????');
                return redirect('invoices');
            }
            $invoices = Invoice::where('id', $id)->first();
            return view('invoices.Print_invoice', compact('invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
        return view('invoices.Print_invoice', compact('invoices'));
>>>>>>> parent of a03dbdd... api commit
    }

    public function export()
    {
        try {
            return Excel::download(new InvoicesExport, 'invoices.xlsx');
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }

    public function Invoice_Paid()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $invoices = Invoice::where('value_status', 1)->get();
        return view('invoices.paid_invoices', compact('invoices'));
=======
        try {
            $invoices = Invoice::where('Value_Status', 1)->get();
            return view('invoices.invoices_paid', compact('invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
=======
        $invoices = Invoice::where('Value_Status', 1)->get();
        return view('invoices.invoices_paid', compact('invoices'));
>>>>>>> parent of a03dbdd... api commit
    }

    public function Invoice_unPaid()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $invoices = Invoice::where('value_status', 2)->get();
        return view('invoices.unpaid_invoices', compact('invoices'));
=======
        $invoices = Invoice::where('Value_Status', 2)->get();
        return view('invoices.invoices_unpaid', compact('invoices'));
>>>>>>> parent of a03dbdd... api commit
    }

    public function Invoice_Partial()
    {
<<<<<<< HEAD
        $invoices = Invoice::where('value_status', 3)->get();
        return view('invoices.partial_invoices', compact('invoices'));
=======
        try {
            $invoices = Invoice::where('Value_Status', 2)->get();
            return view('invoices.invoices_unpaid', compact('invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
=======
        $invoices = Invoice::where('Value_Status', 3)->get();
        return view('invoices.invoices_Partial', compact('invoices'));
>>>>>>> parent of a03dbdd... api commit
    }


    public function Invoice_Partial()
    {
        try {
            $invoices = Invoice::where('Value_Status', 3)->get();
            return view('invoices.invoices_Partial', compact('invoices'));
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
>>>>>>> 37467d0d69a2735db87bc4621599a34d9cf041cb
    }


    public function MarkAsRead_all(Request $request)
    {
        try {
            $userUnreadNotification = auth()->user()->unreadNotifications;

            if ($userUnreadNotification) {
                $userUnreadNotification->markAsRead();
                return back();
            }
        } catch (\Exception $ex) {
            session()->flash('error', '?????? ?????? ???? ???????? ???????????????? ???????? ??????');
            return back();
        }
    }
}
