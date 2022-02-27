<?php

use App\Http\Controllers\Invoice\InvoiceAchiveController;
use App\Http\Controllers\Invoice\InvoiceAttachmentsController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\InvoiceDetailsController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Report\Customer_ReportController;
use App\Http\Controllers\Report\Invoice_ReportController;
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
    Route::post('/Status_Update/{id}', [InvoiceController::class,'Status_Update'])->name('Status_Update');
    Route::get('Print_invoice/{id}',[InvoiceController::class,'Print_invoice']);
    Route::get('export_invoices',[InvoiceController::class,'export']);
    Route::get('Invoice_Paid',[InvoiceController::class,'Invoice_Paid']);
    Route::get('Invoice_UnPaid',[InvoiceController::class,'Invoice_unPaid']);
    Route::get('Invoice_Partial',[InvoiceController::class,'Invoice_Partial']);
    Route::get('MarkAsRead_all',[InvoiceController::class,'MarkAsRead_all'])->name('MarkAsRead_all');
    


    Route::resource('Archive',InvoiceAchiveController::class);
    
    
    Route::get('/InvoicesDetails/{id}',[InvoiceDetailsController::class,'show']);
    Route::get('/download/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'get_file']);
    Route::get('/View_file/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'open_file']);
    Route::Post('/delete_file',[InvoiceDetailsController::class,'destroy'])->name('delete_file');
    
    Route::resource('InvoiceAttachments',InvoiceAttachmentsController::class);
    
    

    
    Route::get('invoices_report',[Invoice_ReportController::class,'index']);
    Route::post('Search_invoices',[Invoice_ReportController::class,'Search_invoices']);

    Route::get('customers_report',[Customer_ReportController::class,'index']);
    Route::post('Search_customers',[Customer_ReportController::class,'Search_customers']);


        
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

});


