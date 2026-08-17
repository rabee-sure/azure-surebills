<?php

use App\Application;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\ChartsController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillSubdomainController;
use App\Http\Controllers\ChannelApplicationController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FandaqahOperationsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\MobileVerifyController;
use App\Http\Controllers\PaymentLogController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PublicMediaController;
use App\Http\Controllers\RefundedBillController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\StoreUserController;
use App\Http\Controllers\TaxInvoiceRequestController;
use App\Http\Controllers\TransferController;
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

Route::domain(config('payment.invoice_subdomain'))->group(function (){
    
  Route::get('.well-known/{file}', [BillSubdomainController::class, 'verifyOwnershipForApplePay'])->name('verify.applepay.ownership');
  Route::get('/bills/{id}/pay', [BillController::class, 'pay'])->name('bill.invoice.subdomain');
  Route::get('/bills/{id}/pay/{lang}', [BillController::class, 'pay'])->name('bill.invoice.lang.subdomain');
  
});

Route::get('/payment-success', [BillController::class, 'paymentSuccess'])->name('paymentsuccess');


// Payments Routes
Route::any('mastercard-webhook', [BillController::class, 'masterCardWebHookResponse'])->name('webhook-success');

Route::get('/set-lang/{lang}', [SettingsController::class, 'changeLang'])->name('changeLang');

Route::get('impersonate/login', [ImpersonateController::class, 'login'])->name('impersonate.login');
Route::get('impersonate/leave', [ImpersonateController::class, 'leave'])->middleware('auth')->name('impersonate.leave');

Route::middleware(['web', 'auth'])->prefix('oauth')->group(function () {
  Route::get('/clients', [ClientController::class, 'forUser'])->name('passport.clients.index');

  Route::post('/clients', [ClientController::class, 'store'])->name('passport.clients.store');

  Route::put('/clients/{client_id}', [ClientController::class, 'update'])->name('passport.clients.update');

  Route::delete('/clients/{client_id}', [ClientController::class, 'destroy'])->name('passport.clients.destroy');
});

Auth::routes();

// OTP verification routes with throttle:3,1 for 3 attempts in 1 minute as per as merchant otp configuration
Route::get('/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify.form');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify')->middleware('throttle:'.config("merchant_otp.throttle_attempts").','.config("merchant_otp.throttle_time"));
Route::post('/resend-otp', [OtpController::class, 'resend'])->name('otp.resend')->middleware('throttle:'.config("merchant_otp.throttle_attempts").','.config("merchant_otp.throttle_time"));

Route::get('login-by-secret/{secret}/{secret2}', [FandaqahOperationsController::class, 'loginBySecret']);

Route::get('user-permissions/{guard?}', [UserController::class, 'getUserPermissions']);
Route::get('merchant-settings', [UserController::class, 'getMerchantsettings']);
Route::get('current-user-admin/{guard?}', [UserController::class, 'getAuthAdminUser']);

Route::get('/bills/{id}/pay/{lang}', [BillController::class, 'pay'])->name('paybillpagelang')->middleware('redirect.to.subdomain');
Route::post('/bills/{id}/pay', [BillController::class, 'postPay'])->name('bills.bay');
Route::get('/bills/{id}/pay', [BillController::class, 'pay'])->name('paybillpage')->middleware('redirect.to.subdomain');

Route::middleware(['auth'])->group(function () {
    Route::get('mobile_verify', [MobileVerifyController::class, 'index'])->name('mobile_verify');
    Route::post('mobile_verify', [MobileVerifyController::class, 'store'])->name('post.mobile_verify');
    Route::post('mobile_verify/resendCode', [MobileVerifyController::class, 'resendCode'])->name('resend_code');

    Route::get('settings', [SettingsController::class, 'settings'])->name('settings');
    Route::post('settings', [SettingsController::class, 'postSettings'])->name('post.settings');

    Route::get('tax_invoice_request', [TaxInvoiceRequestController::class, 'store'])->name('tax_invoice.request');

    Route::get('account', [AccountController::class, 'account'])->name('account');
    Route::get('account/account_information', [AccountController::class, 'account_information'])->name('account_information');
    Route::post('account-information', [AccountController::class, 'storeAccountInformation'])->name('account.information');

    Route::get('account/bank_information', [AccountController::class, 'bank_information'])->name('bank_information');
    Route::post('bank-information', [AccountController::class, 'storeBankInformation'])->name('bank.information');

    Route::get('account/business_information', [AccountController::class, 'business_information'])->name('business_information');
    Route::post('business-information', [AccountController::class, 'storeBusinessInformation'])->name('business.information');

    // download file
    Route::get('download/merchant-document/{collection}/{file}', [AccountController::class, 'downloadMerchantDocument'])->name('download.merchant_document');

    Route::get('download/{id}/{file}', [AccountController::class, 'downloadFile'])->name('download.file');

    Route::get('account/change_password', [AccountController::class, 'changePassword'])->name('change_password');
    Route::post('change-password', [AccountController::class, 'storeChangePassword'])->name('change.password');

    Route::get('pricing', [PricingController::class, 'index'])->name('pricing');
    Route::put('pricing', [PricingController::class, 'update'])->name('update_price');
    Route::get('pricing/details', [PricingController::class, 'details'])->name('details');

    Route::get('/bills/{id}/print', [BillController::class, 'billPrint'])->name('bills.bill_print');
    Route::get('/refundedbills/{id}/print', [RefundedBillController::class, 'billPrint'])->name('refundedbills.bill_print');
    Route::get('/bills/{id}/invoice', [BillController::class, 'invoice'])->name('invoice');
    Route::get('/refundedbills/{id}/invoice', [RefundedBillController::class, 'invoice'])->name('refundedinvoice');
    Route::post('/bills/{id}/cancel', [BillController::class, 'cancel'])->name('bills.cancel');
    Route::post('/bills/{id}/refund', [BillController::class, 'refund'])->name('bills.refund');
    Route::post('/bills/{id}/change_status', [BillController::class, 'changeStatus'])->name('bills.change_status');
    Route::post('/bills/{id}/partial-refund', [BillController::class, 'partialRefund'])->name('bills.partial.refund');
    Route::get('/bills/{hash}/handle-payment', [BillController::class, 'handlePayment'])->name('bills.handle');
});

Route::middleware(['auth', 'mobile.verified', 'profile.completed'])->group(function () {
  Route::apiResource('applications', ApplicationController::class);
  Route::apiResource('channels.applications', ChannelApplicationController::class);
  Route::resource('channels', ChannelController::class);
  Route::resource('bills', BillController::class);
  Route::get('export/bills', [BillController::class, 'export'])->name('export.bills');
  Route::resource('refundedbills', RefundedBillController::class);
  Route::get('bills/debit_note/create/{bill_id}', [BillController::class, 'createDebitNote'])->name('debitNote.create');
  Route::post('bills/debit_note/store', [BillController::class, 'storeDebitNote'])->name('debitNote.store');
  Route::get('/logs/{log}/', [PaymentLogController::class, 'show'])->name('logpage');

  Route::get('customers/search_by_name', [CustomerController::class, 'searchByName'])->name('customers.search_name');
  Route::get('customers/search_by_mobile', [CustomerController::class, 'searchByMobile'])->name('customers.search_mobile');

  Route::resource('customers', CustomerController::class);

  // Coupons routes
  Route::resource('coupons', CouponController::class);
  Route::post('coupons/{id}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
  Route::post('coupons/{id}/delete', [CouponController::class, 'destroy'])->name('coupons.delete');
  Route::get('coupons/{id}/bulk-generate', [CouponController::class, 'bulkGenerate'])->name('coupons.bulk-generate');
  Route::post('coupons/{id}/bulk-generate', [CouponController::class, 'storeBulkGenerate'])->name('coupons.store-bulk-generate');
  Route::get('coupons/{id}/export', [CouponController::class, 'showExport'])->name('coupons.show-export');
  Route::post('coupons/{id}/export', [CouponController::class, 'export'])->name('coupons.export');

  Route::get('statement', [StatementController::class, 'index'])->name('statement.index');
  Route::get('statement/export', [StatementController::class, 'export'])->name('statement.export');
  Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
  Route::get('transfers/{transfer}/bills', [TransferController::class, 'bills'])->name('transfer.bills');
  Route::get('transfers/{transfer}/transactions', [TransferController::class, 'transactions'])->name('transfer.transactions');
  Route::get('transfers/{transfer}/bills/export', [TransferController::class, 'exportTransferBills'])->name('transfer.export_bills');

  Route::post('transfers/request', [TransferController::class, 'request'])->name('transfers.request');


  Route::get('/home', [HomeController::class, 'index'])->name('home');
  Route::get('/terms', [HomeController::class, 'terms']);
  Route::get('/integration', [IntegrationController::class, 'index'])->name('integration');
  Route::get('/integration/documentation', [IntegrationController::class, 'documentation'])->name('integration.documentation');

  Route::get('payment_record', [ReportsController::class, 'paymentRecord'])->name('reports.paymentRecord');
  Route::get('payment_record/export', [ReportsController::class, 'paymentRecordExport'])->name('reports.paymentRecordExport');

  // Roles
  Route::resource('users', StoreUserController::class);
  Route::post('/users/{user}/restore', [StoreUserController::class, 'restore'])->name('users.restore');

  Route::resource('roles', RolesController::class);
});

Route::get('media/{path}', [PublicMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// Backward compatibility: /storage/* URLs without php artisan storage:link
Route::get('storage/{path}', function (string $path) {
    return redirect()->route('media.show', ['path' => $path], 301);
})->where('path', '.*');

Route::get('file/{guard}/{file}', [MediaController::class, 'getFile'])->where('file', '.*');

Route::get('/', [HomeController::class, 'landing']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::prefix('ajax')->group(function () {
  Route::post('contact/send', [HomeController::class, 'contactSendForm'])->name('contact.send_form');
});

Route::middleware(['auth:admins'])->group(function () {
    Route::get('users/all', [UserController::class, 'all'])->name('users.all');
    Route::get('users/{user}/transfers', [UserController::class, 'transfers'])->name('users.transfers');
    Route::get('users/{user}/transactions', [TransferController::class, 'userTransactions'])->name('users.transactions');
    Route::get('users/{user}/alltransactions', [TransferController::class, 'userallTransactions'])->name('users.alltransactions');
    Route::get('users/{user}/bills', [UserController::class, 'bills'])->name('users.bills');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
});

Route::post('images-upload', [AccountController::class, 'imagesUploadPost'])->name('images.upload');

Route::get('/docs/{page?}', [DocumentationController::class, 'index']);

Route::middleware('auth')->prefix('api/v1')->group(function () {
    Route::get('charts/bills_paid_amount', [ChartsController::class, 'billsPaidAmount']);
    Route::get('charts/bills_paid_count', [ChartsController::class, 'billsPaidCount']);
    Route::get('charts/bills_count', [ChartsController::class, 'billsCount']);
});

Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->middleware('validate.referer')->name('password.update');
