<?php

use App\Http\Controllers\Api\Invoice\InvoiceAchiveController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Invoice\InvoiceAttachmentsController;
use App\Http\Controllers\Api\Invoice\InvoiceController;
use App\Http\Controllers\Api\Invoice\InvoiceDetailsController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Report\Customer_ReportController;
use App\Http\Controllers\Api\Report\Invoice_ReportController;
use App\Http\Controllers\Api\Role_Permission\RoleController;
use App\Http\Controllers\Api\Role_Permission\UserController;
use App\Http\Controllers\Api\Section\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group(['middleware' => 'jwt.verify'],function(){
    Route::apiResource('sections',SectionController::class);

    Route::apiResource('products',ProductController::class);

    Route::apiResource('invoices',InvoiceController::class);

    //to get section products for add new invoice
    Route::get('/section/{id}',[InvoiceController::class,'getproducts']);

    //to update status of paid
    Route::post('/Status_Update/{id}', [InvoiceController::class,'Status_Update'])->name('Status_Update');
    Route::get('Print_invoice/{id}',[InvoiceController::class,'Print_invoice']);
    //Route::get('export_invoices',[InvoiceController::class,'export']);
    Route::get('Invoice_Paid',[InvoiceController::class,'Invoice_Paid']);
    Route::get('Invoice_UnPaid',[InvoiceController::class,'Invoice_unPaid']);
    Route::get('Invoice_Partial',[InvoiceController::class,'Invoice_Partial']);
    Route::get('MarkAsRead_all',[InvoiceController::class,'MarkAsRead_all'])->name('MarkAsRead_all');

    Route::resource('Archive',InvoiceAchiveController::class);

    Route::get('/InvoicesDetails/{id}',[InvoiceDetailsController::class,'show']);
    Route::Post('/delete_file',[InvoiceDetailsController::class,'destroy'])->name('delete_file');
    Route::get('/download/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'get_file']);
    Route::get('/View_file/{invoice_number}/{file_name}',[InvoiceDetailsController::class,'open_file']);

    Route::apiResource('InvoiceAttachments',InvoiceAttachmentsController::class);


    //Route::get('invoices_report',[Invoice_ReportController::class,'index']);
    Route::post('Search_invoices',[Invoice_ReportController::class,'Search_invoices']);

    //Route::get('customers_report',[Customer_ReportController::class,'index']);
    Route::post('Search_customers',[Customer_ReportController::class,'Search_customers']);


    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);

});