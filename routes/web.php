<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Customers_ReportController;
use App\Http\Controllers\InvoiceAchiveController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\Invoices_ReportController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;
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
    Route::resource('sections',SectionsController::class);

    Route::resource('products',ProductController::class);

    Route::resource('invoices',InvoicesController::class);
    //to get section products for add new invoice
    Route::get('/section/{id}',[InvoicesController::class,'getproducts']);
    //to update status of paid
    Route::post('/Status_Update/{id}', [InvoicesController::class,'Status_Update'])->name('Status_Update');
    Route::get('Print_invoice/{id}',[InvoicesController::class,'Print_invoice']);
    Route::get('export_invoices',[InvoicesController::class,'export']);
    Route::get('Invoice_Paid',[InvoicesController::class,'Invoice_Paid']);
    Route::get('Invoice_UnPaid',[InvoicesController::class,'Invoice_unPaid']);
    Route::get('Invoice_Partial',[InvoicesController::class,'Invoice_Partial']);
    Route::get('MarkAsRead_all',[InvoicesController::class,'MarkAsRead_all'])->name('MarkAsRead_all');
    


    Route::resource('Archive',InvoiceAchiveController::class);
    
    
    Route::get('/InvoicesDetails/{id}',[InvoicesDetailsController::class,'show']);
    Route::get('/download/{invoice_number}/{file_name}',[InvoicesDetailsController::class,'get_file']);
    Route::get('/View_file/{invoice_number}/{file_name}',[InvoicesDetailsController::class,'open_file']);
    Route::Post('/delete_file',[InvoicesDetailsController::class,'destroy'])->name('delete_file');
    
    Route::resource('InvoiceAttachments',InvoiceAttachmentsController::class);
    
    

    
    Route::get('invoices_report',[Invoices_ReportController::class,'index']);
    Route::post('Search_invoices',[Invoices_ReportController::class,'Search_invoices']);

    Route::get('customers_report',[Customers_ReportController::class,'index']);
    Route::post('Search_customers',[Customers_ReportController::class,'Search_customers']);


        
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

});


