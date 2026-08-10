<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\ZatcaController;
use App\Http\Controllers\Security\CspReportController;
use App\Services\CyberSourceService;
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
/**
 * Routes for test must remove on production
 */

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
Route::post('payment-webhook', [PaymentController::class, 'handleWebhook'])->name('api.payment.webhook');
Route::any('health-check', [PaymentController::class, 'healthCheck'])->name('api.health.check');
Route::post('csp/report', [CspReportController::class, 'store'])->name('csp.report');

Route::post('payment', [PaymentController::class, 'processPayment']);
Route::post('refund/{transactionId}', [PaymentController::class, 'processRefund']);

Route::post('applepay/validate', 'ApplePayController@validateMerchant')->name('mastercard.applepay.validate');
Route::post('applepay/check-payment', 'ApplePayController@checkPayment')->name('mastercard.applepay.check.payment');
Route::post('mastercard/handle-payment', 'MasterCardController@handlePyament')->name('mastercard.handle.payment');
Route::post('mastercard/{session}/check-payment', 'MasterCardController@checkPayment')->name('mastercard.3ds');

/**
 * Cybersource Routes
 */

// Apple Pay
Route::post('cybersource/applepay/validate', [PaymentController::class, 'validateApplePayMerchant'])->name('applepay.validate');
Route::post('cybersource/applepay/check-payment', 'CybersourceApplePayController@checkPayment')->name('applepay.check-payment');

// Payer Setup
Route::post('payer-auth-setup', [PaymentController::class, 'payerAuthSetup'])->name('cybersource.payerAuth.setup');

// Enrollement
Route::post('payer-auth-enrollment', [PaymentController::class, 'checkPayerAuthEnrollment'])->name('cybersource.payerAuth.enrollment.check');

// Callback after enrollement
Route::any('callback-after-enrollement/{billId}/{applePay?}', [PaymentController::class, 'callbackAfterEnrollement'])->name('cybersource.callback.after.enrollement');

// Validation check
Route::post('payer-auth-validation-results', [PaymentController::class, 'validateAuthenticationResults'])->name('cybersource.payerAuth.validation.results');

// Payment
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');

// End Cybersource Routes

Route::post('upload', 'MediaController@upload')->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', 'MediaController@uploadAttachment');

Route::prefix('v1')->group(function () {
	Route::post('onbording', [ZatcaController::class, 'onboarding'])->middleware(['zatca.api'])->name('onbording');
	Route::post('sent-invoice-to-zatca', [ZatcaController::class, 'sendInvoiveToZatca'])->middleware(['zatca.api'])->name('sent-invoice-to-zatca');

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
		// Coupons API routes
		// Controllers resolve under RouteServiceProvider::$api_namespace (App\Http\Controllers\Api).
		// Do not prefix with "Api\" or the target becomes Api\Api\CouponController.
		Route::post('coupons/validate', 'CouponController@validateCoupon')->name('api.coupons.validate');
		Route::get('coupons', 'CouponController@index')->name('api.coupons.index');
		Route::get('coupons/{id}', 'CouponController@show')->name('api.coupons.show');
  });

    // Route::post('fandaqah-register', 'UserController@registerFandaqah');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');

    Route::post('channels/{channel}/add-app', 'ChannelController@addApplication');
    Route::post('channels/{channel}/sub-account', 'ChannelController@subAccount');
    Route::post('channels/{channel}/transactions', 'ChannelController@transactions');
    Route::put('channels/{channel}/update_sub_account_payment_fees', 'ChannelController@updateSubAccountPaymentFees');
});
