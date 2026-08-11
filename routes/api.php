<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ApplePayController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\MasterCardController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ZatcaController;
use App\Http\Controllers\Security\CspReportController;
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

Route::post('csp/report', [CspReportController::class, 'store'])->name('csp.report');

Route::post('applepay/validate', [ApplePayController::class, 'validateMerchant'])->name('mastercard.applepay.validate');
Route::post('applepay/check-payment', [ApplePayController::class, 'checkPayment'])->name('mastercard.applepay.check.payment');
Route::post('mastercard/handle-payment', [MasterCardController::class, 'handlePyament'])->name('mastercard.handle.payment');
Route::post('mastercard/{session}/check-payment', [MasterCardController::class, 'checkPayment'])->name('mastercard.3ds');

Route::post('upload', [MediaController::class, 'upload'])->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', [MediaController::class, 'uploadAttachment']);

Route::prefix('v1')->group(function () {
	Route::post('onbording', [ZatcaController::class, 'onboarding'])->middleware(['zatca.api'])->name('onbording');
	Route::post('sent-invoice-to-zatca', [ZatcaController::class, 'sendInvoiveToZatca'])->middleware(['zatca.api'])->name('sent-invoice-to-zatca');

	//should send application id and secret
	Route::group(['middleware' => ['User.from.application']], function () {
		Route::post('bills/create/wordpress', [BillController::class, 'wordpress'])->middleware(['verified.user']);
		Route::post('bills/create', [BillController::class, 'store'])->middleware(['verified.user']);
		Route::post('bills/{bill}/debitnote/create', [BillController::class, 'storeDebitNote'])->middleware(['verified.user']);
		Route::put('bills/{bill}/cancel', [BillController::class, 'cancel'])->middleware(['verified.user']);
		Route::put('bills/{bill}/timeout', [BillController::class, 'timeout']);
		Route::put('bills/{bill}/refund', [BillController::class, 'refund'])->middleware(['verified.user']);
		Route::post('bills/payment_form', [BillController::class, 'paymentForm'])->middleware(['verified.user']);
		Route::get('bills/{bill}', [BillController::class, 'show']);

		Route::get('transfers/{transfer}/transactions', [TransferController::class, 'transactions']);

		Route::get('transactions', [TransactionController::class, 'index']);
    	Route::get('account/information', [AccountController::class, 'getInformation']);
    	Route::post('account/information', [AccountController::class, 'updateInformation']);


	});

	Route::group(['middleware' => ['auth:api']], function () {
		// Coupons API routes
		Route::post('coupons/validate', [CouponController::class, 'validateCoupon'])->name('api.coupons.validate');
		Route::get('coupons', [CouponController::class, 'index'])->name('api.coupons.index');
		Route::get('coupons/{id}', [CouponController::class, 'show'])->name('api.coupons.show');
  });

    // Route::post('fandaqah-register', [UserController::class, 'registerFandaqah']);
    Route::post('fandaqah-update-redirect', [UserController::class, 'updateRedirect']);

    Route::post('channels/{channel}/add-app', [ChannelController::class, 'addApplication']);
    Route::post('channels/{channel}/sub-account', [ChannelController::class, 'subAccount']);
    Route::post('channels/{channel}/transactions', [ChannelController::class, 'transactions']);
    Route::put('channels/{channel}/update_sub_account_payment_fees', [ChannelController::class, 'updateSubAccountPaymentFees']);
});
