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
Route::post('mastercard/{session}/check-payment', 'MasterCardController@checkPayment')->name('mastercard.3ds');


Route::get('test', 'TestController@test');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('test', 'TestController@test')->name('test');
Route::post('upload', 'MediaController@upload')->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', 'MediaController@uploadAttachment');

Route::prefix('v1')->group(function () {

    Route::post('pos-login', 'UserController@posLogin');

    Route::get('analytics', 'AnalyticsController@index');

    Route::get('charts/bills_paid_amount', 'ChartsController@billsPaidAmount');
	Route::get('charts/bills_paid_count', 'ChartsController@billsPaidCount');
	Route::get('charts/bills_count', 'ChartsController@billsCount');

	Route::get('users/{user}/stats', 'UserController@stats');

	//should send application id and secret
	Route::group(['middleware' => ['User.from.application']], function () {
		Route::post('bills/create/wordpress', 'BillController@wordpress');
		Route::post('bills/create', 'BillController@store');
		Route::put('bills/{bill}/cancel', 'BillController@cancel');
		Route::put('bills/{bill}/timeout', 'BillController@timeout');
		Route::put('bills/{bill}/refund', 'BillController@refund');
		Route::get('bills/{bill}', 'BillController@show');

		Route::get('transfers/{transfer}/transactions', 'TransferController@transactions');

		Route::get('transactions', 'TransactionController@index');
    	Route::get('account/information', 'AccountController@getInformation');
    	Route::post('account/information', 'AccountController@updateInformation');

		//Categories
		Route::get('categories', 'CategoryController@index')->name('categories.index');
		Route::get('top-categories', 'CategoryController@topCategories')->name('categories.top');
		Route::get('sub-categories/{parent}', 'CategoryController@subCategories');
		Route::post('category/store', 'CategoryController@store')->name('categories.store');
		Route::get('categories/{id}', 'CategoryController@show')->name('categories.show');
		Route::post('category/{id}/update', 'CategoryController@update');
		Route::delete('category/{id}/delete', 'CategoryController@delete');

		//Products
		Route::get('products', 'ProductsController@index')->name('products.index');
		Route::get('products/{id}', 'ProductsController@show')->name('products.show');
		Route::post('products/store', 'ProductsController@store')->name('products.store');
		Route::post('products/{id}/update', 'ProductsController@update')->name('products.update');
		Route::delete('products/{id}/delete', 'ProductsController@delete')->name('products.delete');

		//POS
		Route::get('getActiveTopCategory', 'PosController@getActiveTopCategory')->name('pos.active-top-categories');
		Route::get('getActiveSubCategory/{category_id}', 'PosController@getActiveSubCategory');
		Route::get('getActiveCategoryProducts/{category_id}', 'PosController@getActiveCategoryProducts');
		Route::get('getProduct/{product_id}', 'PosController@getProduct');
		Route::get('searchForProduct/{keyword}', 'PosController@searchForProduct');
		Route::get('searchForCustomer/{name}', 'PosController@searchForCustomer');
		Route::post('customerStore', 'PosController@customerStore');

	});

    // Route::post('fandaqah-register', 'UserController@registerFandaqah');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');

    Route::post('channels/{channel}/sub-account', 'ChannelController@subAccount');
    Route::post('channels/{channel}/transactions', 'ChannelController@transactions');
    Route::put('channels/{channel}/update_sub_account_payment_fees', 'ChannelController@updateSubAccountPaymentFees');

    Route::get('banks', 'BankController@index');

	Route::prefix('sps')->group(function () {
		Route::post('transfer_statement', 'SPSController@transferStatement');
	});


});
