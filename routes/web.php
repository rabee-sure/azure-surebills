<?php

use App\Application;
use App\Http\Controllers\CyberSourceController;
use App\Http\Controllers\PaymentController;
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


Route::post('setup-enrollement', [CyberSourceController::class, 'setupEnrollement'])->name('setup-enrollement');

Route::get('iframe-1', function(){

  return view('cyber.iframe-1', ['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkM2Y1NDdkYy1lOWUzLTQ3MzYtOTAxZC02YjY3ZDk0NjU1NWEiLCJpYXQiOjE3NDMwNzEzMDEsImlzcyI6IjVkZDgzYmYwMGU0MjNkMTQ5OGRjYmFjYSIsImV4cCI6MTc0MzA3NDkwMSwiT3JnVW5pdElkIjoiNjczZGFlZTMyOWYwYjc0MzM4NGQ5MjAzIiwiUGF5bG9hZCI6eyJBQ1NVcmwiOiJodHRwczovLzBtZXJjaGFudGFjc3N0YWcuY2FyZGluYWxjb21tZXJjZS5jb20vTWVyY2hhbnRBQ1NXZWIvY3JlcS5qc3AiLCJQYXlsb2FkIjoiZXlKdFpYTnpZV2RsVkhsd1pTSTZJa05TWlhFaUxDSnRaWE56WVdkbFZtVnljMmx2YmlJNklqSXVNaTR3SWl3aWRHaHlaV1ZFVTFObGNuWmxjbFJ5WVc1elNVUWlPaUptTXpGaE5qWXdNUzFrTldObUxUUXlOamt0T0RJM09DMWxPREl6WXpNMU5UZzRaV1FpTENKaFkzTlVjbUZ1YzBsRUlqb2laRGt4WlRrd1ltVXRNVGxqTnkwME5qazFMVGc1WVdFdE56TXdPREZqTW1VeVlqQXhJaXdpWTJoaGJHeGxibWRsVjJsdVpHOTNVMmw2WlNJNklqQXlJbjAiLCJUcmFuc2FjdGlvbklkIjoiU3hVSnVmZjFVRWNuemNvZDBGRTAifSwiT2JqZWN0aWZ5UGF5bG9hZCI6dHJ1ZSwiUmV0dXJuVXJsIjoiaHR0cHM6Ly93djczMGh3NzAzMzI1MDozMDAyL3Jlc3RhcGkvY2FyZGluYWxEaXJlY3QvU3RlcFVwL1Jlc3BvbnNlIn0.Ei9cuzo0iiE-VyEwUuIQsiRAUyqjwJFNlYvJTNE7xrY']);

});


Route::get('iframe-2/{token}', function($token){

  return view('cyber.iframe-2', ['token' => $token]);

})->name('ifreame_2');

// Route::any('validate/payer/auth2','BillSubdomainController@cybersourceReturn2')->name('validate-auth-result-2');

Route::domain(config('payment.invoice_subdomain'))->group(function (){
    
  Route::get('.well-known/{file}', 'BillSubdomainController@verifyOwnershipForApplePay')->name('verify.applepay.ownership');
  Route::get('/bills/{id}/pay', 'BillController@pay')->name('bill.invoice.subdomain');
  Route::get('/bills/{id}/pay/{lang}', 'BillController@pay')->name('bill.invoice.lang.subdomain');

  Route::get('device_data_collect_information/{setupAccessToken}', function($setupAccessToken){
    return view('cyber.otp_form', ['setupAccessToken' => $setupAccessToken]);
  })->name('device_data_collect_information');


  // Route::any('validate/payer/auth','BillSubdomainController@cybersourceReturn')->name('validate-auth-result');
  

});

// Route::get('delete-subscripe-all-products', function () {
//   $cyberSourceWebhookService = new App\Services\CyberSourceWebhookService;
//   $subscribedProducts = $cyberSourceWebhookService->getWebhookSubscriptionsByOrg(config('cybersource.merchant_id'), '', '')[0] ?? [];
//   foreach ($subscribedProducts as $subscribedProduct) {
//     $cyberSourceWebhookService->deleteWebhookSubscription($subscribedProduct['webhookId']);
//   }
// });

// Route::get('subscripe-all-products', function () {

//   $productsList = [
//     [
//       "productId" => "transactionSearch",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "fraudManagementEssentials",
//       "eventTypes" => [
//         "risk.profile.decision.review",
//         "risk.profile.decision.reject",
//         "risk.profile.decision.monitor",
//         "risk.casemanagement.addnote",
//         "risk.casemanagement.decision.accept",
//         "risk.casemanagement.decision.reject",
//         "risk.profile.decision.review.5m",
//         "risk.profile.decision.reject.5m",
//         "risk.profile.decision.monitor.5m",
//         "risk.profile.decision.review.5m",
//         "risk.profile.decision.reject.5m",
//         "risk.profile.decision.monitor.5m"
//       ]
//     ],
//     [
//       "productId" => "secureAcceptance",
//       "eventTypes" => [
//         "sa.order.confirmation"
//       ]
//     ],
//     [
//       "productId" => "accountUpdater",
//       "eventTypes" => [
//         "aura.batch.status.update",
//         "aura.batch.status.update"
//       ]
//     ],
//     [
//       "productId" => "paymentOrchestration",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "recurringBilling",
//       "eventTypes" => [
//         "rbs.subscriptions.charge.pre-notified",
//         "rbs.subscriptions.charge.created",
//         "rbs.subscriptions.charge.failed",
//         "rbs.subscriptions.charge.pre-notified",
//         "rbs.subscriptions.charge.created",
//         "rbs.subscriptions.charge.failed"
//       ]
//     ],
//     [
//       "productId" => "virtualTerminal",
//       "eventTypes" => [
//         "vt.order.receipt",
//         "vt.followon.receipt",
//         "vt.transactions.receipt",
//         "vt.order.receipt",
//         "vt.followon.receipt",
//         "vt.order.receipt",
//         "vt.followon.receipt"
//       ]
//     ],
//     [
//       "productId" => "payByLink",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "customerInvoicing",
//       "eventTypes" => [
//         "invoicing.merchant.invoice.send",
//         "invoicing.merchant.invoice.paid",
//         "invoicing.merchant.invoice.partial-paid",
//         "invoicing.merchant.invoice.cancel",
//         "invoicing.customer.invoice.send",
//         "invoicing.customer.invoice.cancel",
//         "invoicing.customer.invoice.reminder",
//         "invoicing.customer.invoice.overdue-reminder",
//         "invoicing.customer.invoice.paid",
//         "invoicing.customer.invoice.partial-payment",
//         "invoicing.customer.invoice.partial-resend"
//       ]
//     ],
//     [
//       "productId" => "reporting",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "unifiedCheckout",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "tax",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "payerAuthentication",
//       "eventTypes" => []
//     ],
//     [
//       "productId" => "cardProcessing",
//       "eventTypes" => [
//         "payments.payments.accept",
//         "payments.payments.review",
//         "payments.payments.reject",
//         "payments.payments.partial.approval",
//         "payments.reversals.accept",
//         "payments.reversals.reject",
//         "payments.captures.accept",
//         "payments.captures.review",
//         "payments.captures.reject",
//         "payments.refunds.accept",
//         "payments.refunds.reject",
//         "payments.refunds.partial.approval",
//         "payments.credits.accept",
//         "payments.credits.review",
//         "payments.credits.reject",
//         "payments.credits.partial.approval",
//         "payments.voids.accept",
//         "payments.voids.reject"
//       ]
//     ],
//     [
//       "productId" => "tokenManagement",
//       "eventTypes" => [
//         "tms.token.created",
//         "tms.token.updated",
//         "tms.token.pan_updated",
//         "tms.networktoken.updated",
//         "tms.networktoken.provisioned"
//       ]
//     ]
//   ];

//   $cyberSourceWebhookService = new App\Services\CyberSourceWebhookService;
//   // $productsList = $cyberSourceWebhookService->findProductsToSubscribe()[0] ?? [];

//   foreach ($productsList as $productList) {
//     $cyberSourceWebhookService->createWebhookSubscription($productList['productId'], $productList['eventTypes']);
//   }
// });

// Payments Routes
Route::any('mastercard-webhook', 'BillController@masterCardWebHookResponse')->name('webhook-success');
Route::post('payment-webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
Route::any('health-check', [PaymentController::class, 'healthCheck'])->name('health.check');

// Route::get('test/bill', 'TestController@bill');
Route::get('/set-lang/{lang}', 'SettingsController@changeLang')->name('changeLang');

// Route::middleware(['guest'])->group(function () {
//   Route::get('pos/register', 'UserController@posRegister')->name('pos.register');
// });

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
Route::get('login-by-secret/{secret}/{secret2}', 'FandaqahOperationsController@loginBySecret');

Route::get('redirect/to/products/via/pos/{uuid}', 'PosController@redirectToProductsViaPos')->name('redirect.to.products.via.pos');

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

  //Zain 24/2/2022 POS Routes
  // Route::get('pos/categories', 'PosController@categories')->name('pos.categories');
  // Route::get('pos/products', 'PosController@products')->name('pos.products');
  // Route::get('pos/discount', 'PosController@discount')->name('pos.discount');
  // Route::get('pos/quantity', 'PosController@quantity')->name('pos.quantity');
  // Route::get('pos/pay', 'PosController@pay')->name('pos.pay');
  // Route::get('pos/bill', 'PosController@bill')->name('pos.bill');
  // Route::get('pos/client', 'PosController@client')->name('pos.client');

  Route::get('customers/search_by_name', 'CustomerController@searchByName')->name('customers.search_name');
  Route::get('customers/search_by_mobile', 'CustomerController@searchByMobile')->name('customers.search_mobile');

  Route::resource('customers', 'CustomerController');

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

  // Route::get('categories', 'ProductsController@indexCategory')->name('categories.all');
  // Route::get('categories/{id}/view', 'ProductsController@viewCategory')->name('categories.view');
  // Route::get('categories/create', 'ProductsController@createCategory')->name('categories.create');
  // Route::post('categories/create', 'ProductsController@createCategory');
  // Route::get('categories/{id}/edit', 'ProductsController@editCategory')->name('categories.edit');
  // Route::post('categories/{id}/edit', 'ProductsController@editCategory');

  // Route::get('products', 'ProductsController@index')->name('products.all');
  // Route::get('products/{id}/view', 'ProductsController@view')->name('products.view');
  // Route::get('products/{id}/edit', 'ProductsController@edit')->name('products.edit');
  // Route::post('products/{id}/edit', 'ProductsController@edit');
  // Route::get('products/create', 'ProductsController@create')->name('products.create');
  // Route::post('products/create', 'ProductsController@create');

  // Route::get('products/settings', 'ProductsController@settings')->name('products.settings');

  Route::prefix('ajax')->group(function () {
    //Categories
    // Route::get('categories', 'Api\CategoryController@index')->name('categories.index');
    // Route::get('categories/all', 'Api\CategoryController@getAll')->name('categories.get-all');
    // Route::get('top-categories', 'Api\CategoryController@topCategories')->name('categories.top');
    // Route::get('sub-categories/{parent}', 'Api\CategoryController@subCategories');
    // Route::post('category/store', 'Api\CategoryController@store')->name('categories.store');
    // Route::get('categories/{id}', 'Api\CategoryController@show')->name('categories.show');
    // Route::post('category/{id}/update', 'Api\CategoryController@update')->name('categories.update');
    // Route::delete('category/{id}/delete', 'Api\CategoryController@delete')->name('categories.delete');
    // Route::delete('category/{id}/delete-dependency', 'Api\CategoryController@deleteDependency')->name('categories.delete-dependency');
    // Route::post('categories/delete-move', 'Api\CategoryController@deleteMove')->name('categories.delete-move');
    // Route::get('category/{id}/childsCount', 'Api\CategoryController@childsCount')->name('categories.childsCount');
    // Route::get('category/{id}/productsCount', 'Api\CategoryController@productsCount')->name('categories.productsCount');
    //Products
    // Route::get('products', 'Api\ProductsController@index')->name('products.index');
    // Route::get('products/{id}', 'Api\ProductsController@show')->name('products.show');
    // Route::post('products/store', 'Api\ProductsController@store')->name('products.store');
    // Route::post('products/{id}/update', 'Api\ProductsController@update')->name('products.update');
    // Route::delete('products/{id}/delete', 'Api\ProductsController@delete')->name('products.delete');
  });

  // Orders
  Route::get('orders', 'OrdersController@index')->name('orders.all');
  Route::get('orders/view', 'OrdersController@view')->name('orders.view');

  //Payment Record Report
  Route::get('payment_record', 'ReportsController@paymentRecord')->name('reports.paymentRecord');
  Route::get('payment_record/export', 'ReportsController@paymentRecordExport')->name('reports.paymentRecordExport');

  // Roles
  Route::resource('users', 'StoreUserController');
  Route::resource('roles', 'RolesController');
});

Route::get('/', 'HomeController@landing');
Route::get('/contact', 'HomeController@contact')->name('contact');
Route::get('/privacy', 'HomeController@privacy');
Route::get('/terms', 'HomeController@terms');

Route::middleware(['auth:admins'])->group(function () {
    Route::get('users/all', 'UserController@all')->name('users.all');
    Route::get('users/{user}/transfers', 'UserController@transfers')->name('users.transfers');
    Route::get('users/{user}/transactions', 'TransferController@userTransactions')->name('users.transactions');
    Route::get('users/{user}/alltransactions', 'TransferController@userallTransactions')->name('users.alltransactions');
    Route::get('users/{user}/bills', 'UserController@bills')->name('users.bills');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
});

Route::post('images-upload', 'AccountController@imagesUploadPost')->name('images.upload');

Route::middleware(config('nova.middleware', []))->group(function () {
  Route::prefix('nova/jobs')->group(function () {
    Route::queueMonitor();
  });

  Route::get('transfers/all', 'TransferController@all');

  /**this routes moved from ['auth', 'mobile.verified', 'profile.completed'] middleware
   * to config('nova.middleware', []) middleware because it used on nova and nova after apply users and admins features
   * nova didn't have any "mobile verified" and "profile completed" middlewares
   * so please if any one need to use route in nova
   *
   * we need to ask amr for this middleware security
   */
  Route::post('transfers', 'TransferController@store');
  Route::put('transfers/change_status', 'TransferController@changeStatus');
  Route::put('transfers/{transfer}/cancel', 'TransferController@cancel');

  Route::post('request_change_status', 'TaxInvoiceRequestController@changeStatus')->name('tax_invoice.change_status');

  //Reports
  // Route::get('reports', 'ReportsController@index')->name('reports.index');
  // Route::get('reports/merchants-outstanding', 'ReportsController@merchants_outstanding')->name('reports.merchants-outstanding');
  // Route::post('reports/merchants-outstanding/store', 'ReportsController@merchants_outstanding_store')->name('reports.merchants-outstanding-store');

});

Route::get('/docs/{page?}', 'DocumentationController@index');
// Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');

Route::middleware('auth')->prefix('api/v1')->group(function () {
    Route::get('charts/bills_paid_amount', 'Api\ChartsController@billsPaidAmount');
    Route::get('charts/bills_paid_count', 'Api\ChartsController@billsPaidCount');
    Route::get('charts/bills_count', 'Api\ChartsController@billsCount');
});

Route::middleware(['auth:admins'])->prefix('api/v1')->group(function () {
    Route::get('users/{user}/stats', 'Api\UserController@stats');
    Route::get('analytics', 'Api\AnalyticsController@index');
});

Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->middleware('validate.referer')->name('password.update');
