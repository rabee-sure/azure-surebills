<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\ZatcaController;
use App\Services\CyberSourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillSubdomainController;
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
/**
 * Routes for test must remove on production
 */

 Route::any('validate/payer/auth', [BillSubdomainController::class, 'cybersourceReturn'])->name('validate-auth-result');


// Payer Setup
Route::post('payer-auth-setup', [PaymentController::class, 'payerAuthSetup'])->name('cybersource.payerAuth.setup');

// Enrollement
Route::post('payer-auth-enrollment', [PaymentController::class, 'checkPayerAuthEnrollment'])->name('cybersource.payerAuth.enrollment.check');

// Validation check
Route::post('payer-auth-validation-results', [PaymentController::class, 'validateAuthenticationResults'])->name('cybersource.payerAuth.validation.results');

// Payment
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');

// Route::post('reverse-transaction-simulation', function(Request $request){
// 	// dd($request->header('secret-key'), $request->transaction_id);
// 	if($request->header('secret-key') != "c8080539-ce2b-471e-8c18-d5a073ee6471"){
// 		return response()->json('unauthorized', 422);
// 	}

// 	$cyberSourceService = new CyberSourceService;
// 	$transactionDetails = $cyberSourceService->checkTransaction($request->transaction_id);
// 	$completePaymentCycle = $cyberSourceService->createRevirseTransaction($transactionDetails);
// 	if ($completePaymentCycle['status']) {
// 		return response()->json($completePaymentCycle['type'].' TRANSACTION SUCCESS', 200);
// 	} else {
// 		return response()->json($completePaymentCycle['type'].' TRANSACTION REVERSED', 200);
// 	}
// });
// end test routes
Route::post('payment-webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
Route::any('health-check', [PaymentController::class, 'healthCheck'])->name('health.check');

Route::post('payment', [PaymentController::class, 'processPayment']);
Route::post('refund/{transactionId}', [PaymentController::class, 'processRefund']);

Route::post('applepay/validate', 'ApplePayController@validateMerchant')->name('mastercard.applepay.validate');
Route::post('applepay/check-payment', 'ApplePayController@checkPayment')->name('mastercard.applepay.check.payment');
Route::post('mastercard/handle-payment', 'MasterCardController@handlePyament')->name('mastercard.handle.payment');
Route::post('mastercard/{session}/check-payment', 'MasterCardController@checkPayment')->name('mastercard.3ds');

Route::post('cybersource/applepay/validate', 'CybersourceApplePayController@validateMerchant')->name('applepay.validate');
Route::post('cybersource/applepay/check-payment', 'CybersourceApplePayController@checkPayment')->name('applepay.check-payment');

Route::post('upload', 'MediaController@upload')->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', 'MediaController@uploadAttachment');

Route::prefix('v1')->group(function () {
	Route::post('onbording', [ZatcaController::class, 'onboarding'])->middleware(['zatca.api'])->name('onbording');
	Route::post('sent-invoice-to-zatca', [ZatcaController::class, 'sendInvoiveToZatca'])->middleware(['zatca.api'])->name('sent-invoice-to-zatca');
	Route::group(['middleware' => ['Mutli.auth']], function () {
		//Categories
		// Route::get('categories', 'CategoryController@index');
		// Route::get('categories/all', 'Api\CategoryController@getAll');
		// Route::get('top-categories', 'CategoryController@topCategories');
		// Route::get('sub-categories/{parent}', 'CategoryController@subCategories');
		// Route::post('category/store', 'CategoryController@store');
		// Route::get('categories/{id}', 'CategoryController@show');
		// Route::post('category/{id}/update', 'CategoryController@update');
		// Route::delete('category/{id}/delete', 'CategoryController@delete');
		// Route::delete('category/{id}/delete-dependency', 'Api\CategoryController@deleteDependency');
		// Route::post('categoriesdelete-move', 'Api\CategoryController@deleteMove');
		// Route::get('category/{id}/childsCount', 'Api\CategoryController@childsCount');
        // Route::get('category/{id}/productsCount', 'Api\CategoryController@productsCount');

		//Products
		// Route::get('products', 'ProductsController@index');
		// Route::get('products/{id}', 'ProductsController@show');
		// Route::post('products/store', 'ProductsController@store');
		// Route::post('products/{id}/update', 'ProductsController@update');
		// Route::delete('products/{id}/delete', 'ProductsController@delete');
	});

	//should send application id and secret
	Route::group(['middleware' => ['User.from.application']], function () {
		Route::post('bills/create/wordpress', 'BillController@wordpress')->middleware(['verified.user']);
		Route::post('bills/create', 'BillController@store')->middleware(['verified.user']);
		Route::post('bills/{bill}/debitnote/create', 'BillController@storeDebitNote')->middleware(['verified.user']);
		Route::put('bills/{bill}/cancel', 'BillController@cancel')->middleware(['verified.user']);
		Route::put('bills/{bill}/timeout', 'BillController@timeout');
		Route::put('bills/{bill}/refund', 'BillController@refund')->middleware(['verified.user']);
		Route::post('bills/payment_form', 'BillController@paymentForm')->middleware(['verified.user']);
		Route::get('bills/{bill}', 'BillController@show');

		Route::get('transfers/{transfer}/transactions', 'TransferController@transactions');

		Route::get('transactions', 'TransactionController@index');
    	Route::get('account/information', 'AccountController@getInformation');
    	Route::post('account/information', 'AccountController@updateInformation');


	});

	Route::group(['middleware' => ['auth:api']], function () {
		//POS
		// Route::get('getAllActiveCategoryAndProducts', 'PosController@getAllActiveCategoryAndProducts');
		// Route::get('getActiveTopCategory', 'PosController@getActiveTopCategory');
		// Route::get('getActiveSubCategory/{category_id}', 'PosController@getActiveSubCategory');
		// Route::get('getActiveCategoryProducts/{category_id}', 'PosController@getActiveCategoryProducts');
		// Route::get('getActiveProducts', 'PosController@getActiveProducts');
		// Route::get('getProduct/{product_id}', 'PosController@getProduct');
		// Route::get('searchForProduct/{keyword}', 'PosController@searchForProduct');
		// Route::get('searchForCustomer/{name}', 'PosController@searchForCustomer');
		// Route::post('customerStore', 'PosController@customerStore');
		// Route::post('orderStore', 'PosController@orderStore');
		// Route::post('billChangeStatus', 'PosController@billChangeStatus')->middleware('valid_signture');
		// Route::get('getBills', 'PosController@getBills');
		// Route::get('getBill/{id}', 'PosController@getBill');
		// Route::post('sendBillByEmail', 'PosController@sendBillByEmail');
		// Route::post('setPosUserSetting', 'PosController@setUserSetting');
        // Route::post('redirectToBillsProducts', 'PosController@redirectToBillsProducts');
  });

    // Route::post('fandaqah-register', 'UserController@registerFandaqah');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');

    Route::post('channels/{channel}/add-app', 'ChannelController@addApplication');
    Route::post('channels/{channel}/sub-account', 'ChannelController@subAccount');
    Route::post('channels/{channel}/transactions', 'ChannelController@transactions');
    Route::put('channels/{channel}/update_sub_account_payment_fees', 'ChannelController@updateSubAccountPaymentFees');
});
