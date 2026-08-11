<?php

use App\Application;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\PublicMediaController;
use App\Http\Controllers\StoreUserController;
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

Route::domain(config('payment.invoice_subdomain'))->group(function (){
    
  Route::get('.well-known/{file}', 'BillSubdomainController@verifyOwnershipForApplePay')->name('verify.applepay.ownership');
  Route::get('/bills/{id}/pay', 'BillController@pay')->name('bill.invoice.subdomain');
  Route::get('/bills/{id}/pay/{lang}', 'BillController@pay')->name('bill.invoice.lang.subdomain');
  
});

Route::get('/payment-success', 'BillController@paymentSuccess')->name('paymentsuccess');


// Payments Routes
Route::any('mastercard-webhook', 'BillController@masterCardWebHookResponse')->name('webhook-success');

Route::get('/set-lang/{lang}', 'SettingsController@changeLang')->name('changeLang');

Route::get('impersonate/login', [ImpersonateController::class, 'login'])->name('impersonate.login');
Route::get('impersonate/leave', [ImpersonateController::class, 'leave'])->middleware('auth')->name('impersonate.leave');

Route::middleware(['web', 'auth'])->prefix('oauth')->group(function () {
  Route::get('/clients', [
    'uses' => 'ClientController@forUser',
    'as' => 'passport.clients.index',
  ]);

  Route::post('/clients', [
    'uses' => 'ClientController@store',
    'as' => 'passport.clients.store',
  ]);

  Route::put('/clients/{client_id}', [
    'uses' => 'ClientController@update',
    'as' => 'passport.clients.update',
  ]);

  Route::delete('/clients/{client_id}', [
    'uses' => 'ClientController@destroy',
    'as' => 'passport.clients.destroy',
  ]);
});

Auth::routes();

// OTP verification routes with throttle:3,1 for 3 attempts in 1 minute as per as merchant otp configuration
Route::get('/verify-otp', 'Auth\OtpController@showVerifyForm')->name('otp.verify.form');
Route::post('/verify-otp', 'Auth\OtpController@verify')->name('otp.verify')->middleware('throttle:'.config("merchant_otp.throttle_attempts").','.config("merchant_otp.throttle_time"));
Route::post('/resend-otp', 'Auth\OtpController@resend')->name('otp.resend')->middleware('throttle:'.config("merchant_otp.throttle_attempts").','.config("merchant_otp.throttle_time"));

Route::get('login-by-secret/{secret}/{secret2}', 'FandaqahOperationsController@loginBySecret');

Route::get('user-permissions/{guard?}', 'UserController@getUserPermissions');
Route::get('merchant-settings', 'UserController@getMerchantsettings');
Route::get('current-user-admin/{guard?}', 'UserController@getAuthAdminUser');

Route::get('/bills/{id}/pay/{lang}', 'BillController@pay')->name('paybillpagelang')->middleware('redirect.to.subdomain');
Route::post('/bills/{id}/pay', 'BillController@postPay')->name('bills.bay');
Route::get('/bills/{id}/pay', 'BillController@pay')->name('paybillpage')->middleware('redirect.to.subdomain');

Route::middleware(['auth'])->group(function () {
    Route::get('mobile_verify', 'MobileVerifyController@index')->name('mobile_verify');
    Route::post('mobile_verify', 'MobileVerifyController@store')->name('post.mobile_verify');
    Route::post('mobile_verify/resendCode', 'MobileVerifyController@resendCode')->name('resend_code');

    Route::get('settings', 'SettingsController@settings')->name('settings');
    Route::post('settings', 'SettingsController@postSettings')->name('post.settings');

    Route::get('tax_invoice_request', 'TaxInvoiceRequestController@store')->name('tax_invoice.request');

    Route::get('account', 'AccountController@account')->name('account');
    Route::get('account/account_information', 'AccountController@account_information')->name('account_information');
    Route::post('account-information', 'AccountController@storeAccountInformation')->name('account.information');

    Route::get('account/bank_information', 'AccountController@bank_information')->name('bank_information');
    Route::post('bank-information', 'AccountController@storeBankInformation')->name('bank.information');

    Route::get('account/business_information', 'AccountController@business_information')->name('business_information');
    Route::post('business-information', 'AccountController@storeBusinessInformation')->name('business.information');

    // download file
    Route::get('download/merchant-document/{collection}/{file}', 'AccountController@downloadMerchantDocument')->name('download.merchant_document');

    Route::get('download/{id}/{file}', 'AccountController@downloadFile')->name('download.file');

    Route::get('account/change_password', 'AccountController@changePassword')->name('change_password');
    Route::post('change-password', 'AccountController@storeChangePassword')->name('change.password');

    Route::get('pricing', 'PricingController@index')->name('pricing');
    Route::put('pricing', 'PricingController@update')->name('update_price');
    Route::get('pricing/details', 'PricingController@details')->name('details');

    Route::get('/bills/{id}/print', 'BillController@billPrint')->name('bills.bill_print');
    Route::get('/refundedbills/{id}/print', 'RefundedBillController@billPrint')->name('refundedbills.bill_print');
    Route::get('/bills/{id}/invoice', 'BillController@invoice')->name('invoice');
    Route::get('/refundedbills/{id}/invoice', 'RefundedBillController@invoice')->name('refundedinvoice');
    Route::post('/bills/{id}/cancel', 'BillController@cancel')->name('bills.cancel');
    Route::post('/bills/{id}/refund', 'BillController@refund')->name('bills.refund');
    Route::post('/bills/{id}/change_status', 'BillController@changeStatus')->name('bills.change_status');
    Route::post('/bills/{id}/partial-refund', 'BillController@partialRefund')->name('bills.partial.refund');
    Route::get('/bills/{hash}/handle-payment', 'BillController@handlePayment')->name('bills.handle');
});

Route::middleware(['auth', 'mobile.verified', 'profile.completed'])->group(function () {
  Route::apiResource('applications', 'ApplicationController');
  Route::apiResource('channels.applications', 'ChannelApplicationController');
  Route::resource('channels', 'ChannelController');
  Route::resource('bills', 'BillController');
  Route::get('export/bills', 'BillController@export')->name('export.bills');
  Route::resource('refundedbills', 'RefundedBillController');
  Route::get('bills/debit_note/create/{bill_id}', 'BillController@createDebitNote')->name('debitNote.create');
  Route::post('bills/debit_note/store', 'BillController@storeDebitNote')->name('debitNote.store');
  Route::get('/logs/{log}/', 'PaymentLogController@show')->name('logpage');

  Route::get('customers/search_by_name', 'CustomerController@searchByName')->name('customers.search_name');
  Route::get('customers/search_by_mobile', 'CustomerController@searchByMobile')->name('customers.search_mobile');

  Route::resource('customers', 'CustomerController');

  // Coupons routes
  Route::resource('coupons', 'CouponController');
  Route::post('coupons/{id}/toggle-status', 'CouponController@toggleStatus')->name('coupons.toggle-status');
  Route::post('coupons/{id}/delete', 'CouponController@destroy')->name('coupons.delete');
  Route::get('coupons/{id}/bulk-generate', 'CouponController@bulkGenerate')->name('coupons.bulk-generate');
  Route::post('coupons/{id}/bulk-generate', 'CouponController@storeBulkGenerate')->name('coupons.store-bulk-generate');
  Route::get('coupons/{id}/export', 'CouponController@showExport')->name('coupons.show-export');
  Route::post('coupons/{id}/export', 'CouponController@export')->name('coupons.export');

  Route::get('statement', 'StatementController@index')->name('statement.index');
  Route::get('statement/export', 'StatementController@export')->name('statement.export');
  Route::get('transfers', 'TransferController@index')->name('transfers.index');
  Route::get('transfers/{transfer}/bills', 'TransferController@bills')->name('transfer.bills');
  Route::get('transfers/{transfer}/transactions', 'TransferController@transactions')->name('transfer.transactions');
  Route::get('transfers/{transfer}/bills/export', 'TransferController@exportTransferBills')->name('transfer.export_bills');

  Route::post('transfers/request', 'TransferController@request')->name('transfers.request');


  Route::get('/home', 'HomeController@index')->name('home');
  Route::get('/terms', 'HomeController@terms');
  Route::get('/integration', 'IntegrationController@index')->name('integration');
  Route::get('/integration/documentation', 'IntegrationController@documentation')->name('integration.documentation');

  Route::get('payment_record', 'ReportsController@paymentRecord')->name('reports.paymentRecord');
  Route::get('payment_record/export', 'ReportsController@paymentRecordExport')->name('reports.paymentRecordExport');

  // Roles
  Route::resource('users', 'StoreUserController');
  Route::post('/users/{user}/restore', [StoreUserController::class, 'restore'])->name('users.restore');

  Route::resource('roles', 'RolesController');
});

Route::get('media/{path}', [PublicMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// Backward compatibility: /storage/* URLs without php artisan storage:link
Route::get('storage/{path}', function (string $path) {
    return redirect()->route('media.show', ['path' => $path], 301);
})->where('path', '.*');

Route::get('file/{guard}/{file}', [MediaController::class, 'getFile'])->where('file', '.*');

Route::get('/', 'HomeController@landing');
Route::get('/contact', 'HomeController@contact');
Route::get('/privacy', 'HomeController@privacy');
Route::get('/terms', 'HomeController@terms');
Route::prefix('ajax')->group(function () {
  Route::post('contact/send', 'HomeController@contactSendForm')->name('contact.send_form');
});

Route::middleware(['auth:admins'])->group(function () {
    Route::get('users/all', 'UserController@all')->name('users.all');
    Route::get('users/{user}/transfers', 'UserController@transfers')->name('users.transfers');
    Route::get('users/{user}/transactions', 'TransferController@userTransactions')->name('users.transactions');
    Route::get('users/{user}/alltransactions', 'TransferController@userallTransactions')->name('users.alltransactions');
    Route::get('users/{user}/bills', 'UserController@bills')->name('users.bills');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
});

Route::post('images-upload', 'AccountController@imagesUploadPost')->name('images.upload');

Route::get('/docs/{page?}', 'DocumentationController@index');

Route::middleware('auth')->prefix('api/v1')->group(function () {
    Route::get('charts/bills_paid_amount', 'Api\ChartsController@billsPaidAmount');
    Route::get('charts/bills_paid_count', 'Api\ChartsController@billsPaidCount');
    Route::get('charts/bills_count', 'Api\ChartsController@billsCount');
});

Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->middleware('validate.referer')->name('password.update');
