<?php

use App\Http\Controllers\Invoice\InvoiceAchiveController;
use App\Http\Controllers\Invoice\InvoiceAttachmentsController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\InvoiceDetailsController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Report\CustomerReportController;
use App\Http\Controllers\Report\InvoiceReportController;
use App\Http\Controllers\Role_Permission\RoleController;
use App\Http\Controllers\Role_Permission\UserController;
use App\Http\Controllers\Section\SectionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
//Auth::routes(['register'=>false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('checkStatus');


Route::group(['middleware' => 'auth:web'],function(){
    Route::resource('sections',SectionController::class);

    Route::resource('products',ProductController::class);

    Route::resource('invoices',InvoiceController::class);
    
    //to get section products for add new invoice
    Route::get('/section/{id}',[InvoiceController::class,'getproducts']);
    //to update status of paid
    Route::post('/status-update/{id}', [InvoiceController::class,'statusUpdate'])->name('status_update');
    Route::get('/print-invoice/{id}',[InvoiceController::class,'printInvoice'])->name('print_invoice');
    Route::get('/export-invoice',[InvoiceController::class,'export'])->name('export_invoice');
    Route::get('/paid-invoices',[InvoiceController::class,'paidIvoices'])->name('paid_invoices');
    Route::get('/unpaid-invoices',[InvoiceController::class,'unpaidInvoices'])->name('unpaid_invoices');
    Route::get('/partial-invoices',[InvoiceController::class,'partialInvoices'])->name('partial_invoices');
    Route::get('/MarkAsRead_all',[InvoiceController::class,'MarkAsRead_all'])->name('MarkAsRead_all');
    


    Route::resource('archives',InvoiceAchiveController::class);
    
    
    Route::get('/invoice-details/{id}',[InvoiceDetailsController::class,'show'])->name('invoice_details');
    Route::Post('/delete-file',[InvoiceDetailsController::class,'destroy'])->name('delete_file');
    Route::get('/download/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'getFile'])->name('download_file');
    Route::get('/view-file/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'openFile'])->name('view_file');
    
    Route::resource('invoice-attachments',InvoiceAttachmentsController::class);
    
    

    
    Route::get('invoices-report',[InvoiceReportController::class,'index'])->name('invoices_report');
    Route::post('search-invoices',[InvoiceReportController::class,'search_invoices'])->name('search_invoices');

    Route::get('customers-report',[CustomerReportController::class,'index'])->name('customers_report');
    Route::post('search-customers',[CustomerReportController::class,'search_customers'])->name('search_customers');


        
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);





});


