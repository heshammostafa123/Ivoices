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
use App\Models\Invoice_details;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceDetails;
use App\Models\Section;
use App\Models\User;
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
        return view('invoices.create', compact('sections'));
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
            DB::beginTransaction();

            $invoice= $request->merge(['status'=>'غير مدفوعة','value_status'=>2])->except(['pic']);
            Invoice::create($invoice);

            $invoice_id = Invoice::latest()->first()->id;
            $invoiceDetails=$request->only(['invoice_number','product','section_id','note'])+(['invoice_id'=>$invoice_id,'status'=>'غير مدفوعة','value_status'=>2,'user'=>Auth::user()->name]);
            InvoiceDetails::create($invoiceDetails);

           if ($request->hasFile('pic')) {

                $image = $request->file('pic');
                $file_name = $image->getClientOriginalName();
                $invoice_number = $request->invoice_number;

                InvoiceAttachment::create([
                    'file_name' => $file_name,
                    'invoice_number' => $invoice_number,
                    'created_by' => Auth::user()->name,
                    'invoice_id' => $invoice_id,
                ]);
                // move pic
                $imageName = $request->pic->getClientOriginalName();
                $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
            }


            $user = User::first();
            Notification::send($user, new AddInvoice($invoice_id));

            //$user = User::get();
            //send mail to users with specific permissions
            $user=User::permission('الاشعارات')->get();
            $invoices = Invoice::latest()->first();
            Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));

            DB::commit();
            session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
            return redirect()->route('invoices.index');
        } catch (\Exception $th) {
            DB::rollBack();
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return redirect()->route('invoices.index');
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
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $sections = Section::all();
        return view('invoices.edit', compact('sections', 'invoices'));
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
            $invoices->update($request->except(['invoice_id']));
            session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
            return back();
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
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
            $Details = InvoiceAttachment::where('invoice_id', $id)->first(); ////to know the directory of attachments

            $id_page = $request->id_page;
            
            if (!$id_page == 2) {
                ///delete
                if (!empty($Details->invoice_number)) {

                    Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
                }

                $invoices->forceDelete();
                session()->flash('تم حذف الفاتوره بنجاح');
                return redirect()->route('invoices.index');
            } else {
                //archive
                $invoices->delete();
                session()->flash('تم ارشفة الفاتوره بنجاح');
                return redirect()->route('archives.index');
            }
        } catch (\Exception $th) {
            session()->flash('error', 'حدث خطا ما يرجي المحاوله فيما بعد');
            return back();
        }
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function statusUpdate($id, Request $request)
    {
        $invoices = Invoice::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update($request->only(['status','payment_date'])+['value_status'=>1]);

            InvoiceDetails::create($request->merge(['value_status'=>1,'user'=>Auth::user()->name])->toArray());
        } else {
            $invoices->update($request->only(['status','payment_date'])+['value_status'=>3]);
            InvoiceDetails::create($request->merge(['value_status'=>3,'user'=>Auth::user()->name])->toArray());
        }
        session()->flash('Status_Update');
        return redirect()->route('invoices.index');
    }

    public function printInvoice($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.print_invoice', compact('invoices'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function paidIvoices()
    {
        $invoices = Invoice::where('value_status', 1)->get();
        return view('invoices.paid_invoices', compact('invoices'));
    }

    public function unpaidInvoices()
    {
        $invoices = Invoice::where('value_status', 2)->get();
        return view('invoices.unpaid_invoices', compact('invoices'));
    }

    public function partialInvoices()
    {
        $invoices = Invoice::where('value_status', 3)->get();
        return view('invoices.partial_invoices', compact('invoices'));
    }


    public function MarkAsRead_all(Request $request)
    {

        $userUnreadNotification = auth()->user()->unreadNotifications;

        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }



}
