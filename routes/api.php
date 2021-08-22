<?php

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

Route::post('applepay/validate', 'ApplePayController@validateMerchant');
Route::post('applepay/check-payment', 'ApplePayController@checkPayment');
Route::post('mastercard/handle-payment', 'MasterCardController@handlePyament');
Route::post('mastercard/{session}/check-payment/{from_iframe?}', 'MasterCardController@checkPayment')->name('mastercard.3ds');

Route::get('test', 'TestController@test');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('test', 'TestController@test')->name('test');
Route::post('upload', 'MediaController@upload')->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', 'MediaController@uploadAttachment');

Route::prefix('v1')->group(function () {
    Route::get('analytics', 'AnalyticsController@index');

    Route::get('charts/bills_paid_amount', 'ChartsController@billsPaidAmount');
	Route::get('charts/bills_paid_count', 'ChartsController@billsPaidCount');
	Route::get('charts/bills_count', 'ChartsController@billsCount');

	Route::get('users/{user}/stats', 'UserController@stats');
	
	Route::group(['middleware' => ['User.from.application']], function () {
		Route::post('bills/create/wordpress', 'BillController@wordpress');
		Route::post('bills/create', 'BillController@store');
		Route::put('bills/{bill}/cancel', 'BillController@cancel');
		Route::put('bills/{bill}/timeout', 'BillController@timeout');
		Route::put('bills/{bill}/refund', 'BillController@refund');
		Route::get('bills/{bill}', 'BillController@show');
	});
	Route::get('transfers/{transfer}/transactions', 'TransferController@transactions');

    // Route::post('fandaqah-register', 'UserController@registerFandaqah');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');

    Route::post('channels/{channel}/sub-account', 'ChannelController@subAccount');
    Route::post('channels/{channel}/transactions', 'ChannelController@transactions');
    Route::put('channels/{channel}/update_sub_account_payment_fees', 'ChannelController@updateSubAccountPaymentFees');

    //should send application id and secret
    Route::group(['middleware' => ['User.from.application']], function () {
    	Route::post('transactions', 'TransactionController@index');
    	Route::get('account/information', 'AccountController@getInformation');
    	Route::post('account/information', 'AccountController@updateInformation');
	});
    Route::get('banks', 'BankController@index');


});
