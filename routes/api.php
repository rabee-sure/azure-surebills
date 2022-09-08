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


// Route::get('test', 'TestController@test');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('test', 'TestController@test')->name('test');
Route::post('upload', 'MediaController@upload')->name('media.upload');
Route::post('transfers/{transfer}/upload_attachment', 'MediaController@uploadAttachment');

Route::prefix('v1')->group(function () {

    Route::post('pos-login', 'UserController@posLogin');
    Route::get('report-permissions', 'UserController@reportPermission');

    Route::get('analytics', 'AnalyticsController@index');

    Route::get('charts/bills_paid_amount', 'ChartsController@billsPaidAmount');
	Route::get('charts/bills_paid_count', 'ChartsController@billsPaidCount');
	Route::get('charts/bills_count', 'ChartsController@billsCount');

	Route::get('users/{user}/stats', 'UserController@stats');

	Route::group(['middleware' => ['Mutli.auth']], function () {
		//Categories
		Route::get('categories', 'CategoryController@index');
		Route::get('categories/all', 'Api\CategoryController@getAll');
		Route::get('top-categories', 'CategoryController@topCategories');
		Route::get('sub-categories/{parent}', 'CategoryController@subCategories');
		Route::post('category/store', 'CategoryController@store');
		Route::get('categories/{id}', 'CategoryController@show');
		Route::post('category/{id}/update', 'CategoryController@update');
		Route::delete('category/{id}/delete', 'CategoryController@delete');
		Route::delete('category/{id}/delete-dependency', 'Api\CategoryController@deleteDependency');
		Route::post('categoriesdelete-move', 'Api\CategoryController@deleteMove');
		Route::get('category/{id}/childsCount', 'Api\CategoryController@childsCount');
        Route::get('category/{id}/productsCount', 'Api\CategoryController@productsCount');

		//Products
		Route::get('products', 'ProductsController@index');
		Route::get('products/{id}', 'ProductsController@show');
		Route::post('products/store', 'ProductsController@store');
		Route::post('products/{id}/update', 'ProductsController@update');
		Route::delete('products/{id}/delete', 'ProductsController@delete');
	});

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

	});

	Route::group(['middleware' => ['auth:api']], function () {
		//POS
		Route::get('getAllActiveCategoryAndProducts', 'PosController@getAllActiveCategoryAndProducts');
		Route::get('getActiveTopCategory', 'PosController@getActiveTopCategory');
		Route::get('getActiveSubCategory/{category_id}', 'PosController@getActiveSubCategory');
		Route::get('getActiveCategoryProducts/{category_id}', 'PosController@getActiveCategoryProducts');
		Route::get('getActiveProducts', 'PosController@getActiveProducts');
		Route::get('getProduct/{product_id}', 'PosController@getProduct');
		Route::get('searchForProduct/{keyword}', 'PosController@searchForProduct');
		Route::get('searchForCustomer/{name}', 'PosController@searchForCustomer');
		Route::post('customerStore', 'PosController@customerStore');
		Route::post('orderStore', 'PosController@orderStore');
		Route::get('getBills', 'PosController@getBills');
		Route::get('getBill/{id}', 'PosController@getBill');
		Route::post('sendBillByEmail', 'PosController@sendBillByEmail');
		Route::post('setPosUserSetting', 'PosController@setUserSetting');
  });

    // Route::post('fandaqah-register', 'UserController@registerFandaqah');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');

    Route::post('channels/{channel}/add-app', 'ChannelController@addApplication');
    Route::post('channels/{channel}/sub-account', 'ChannelController@subAccount');
    Route::post('channels/{channel}/transactions', 'ChannelController@transactions');
    Route::put('channels/{channel}/update_sub_account_payment_fees', 'ChannelController@updateSubAccountPaymentFees');

    Route::get('banks', 'BankController@index');

	Route::prefix('sps')->group(function () {
		Route::post('transfer_statement', 'SPSController@transferStatement');
	});


});
