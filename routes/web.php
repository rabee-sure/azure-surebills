<?php

use App\Application;
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

Route::get('test', 'TestController@test');

Route::get('/set-lang/{lang}', 'SettingsController@changeLang')->name('changeLang');

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

Route::middleware(['auth'])->group(function () {
	Route::get('mobile_verify', 'MobileVerifyController@index')->name('mobile_verify');
	Route::post('mobile_verify', 'MobileVerifyController@store')->name('post.mobile_verify');
	Route::post('mobile_verify/resendCode', 'MobileVerifyController@resendCode')->name('resend_code');

	Route::get('settings', 'SettingsController@settings')->name('settings');
	Route::post('settings', 'SettingsController@postSettings')->name('post.settings');

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

});

Route::get('/logs/{log}/', 'BillController@log')->name('logpage');

// py bill page
Route::get('/bills/{id}/pay', 'BillController@pay')->name('paybillpage');
Route::get('/bills/payment_iframe/{id}/{method}/{locale}', 'BillController@payment_iframe')->name('payment_iframe');
Route::get('/bills/{id}/pay/{lang}', 'BillController@pay')->name('paybillpagelang');
Route::post('/bills/{id}/pay', 'BillController@postPay')->name('bills.bay');
Route::post('/bills/{id}/cancel', 'BillController@cancel')->name('bills.cancel');
Route::get('/bills/{hash}/handle-payment', 'BillController@handlePayment')->name('bills.handle');

Route::middleware(['auth', 'mobile.verified', 'profile.completed'])->group(function () {
	Route::resource('applications', 'ApplicationController');
	Route::get('statement', 'StatementController@index')->name('statement.index');
    Route::get('transfer', 'TransferController@index')->name('transfer.index');
    Route::post('transfers', 'TransferController@store');
	Route::resource('bills', 'BillController');
	Route::get('customers/search_by_name', 'CustomerController@searchByName')->name('customers.search_name');
	Route::get('customers/search_by_mobile', 'CustomerController@searchByMobile')->name('customers.search_mobile');
	Route::resource('customers', 'CustomerController');

	Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/terms', 'HomeController@terms');
	Route::get('/integration', 'IntegrationController@index')->name('integration');
	Route::get('/integration/documentation', 'IntegrationController@documentation')->name('integration.documentation');

    Route::get('products', 'ProductsController@index')->name('products.all');
    Route::get('products/{id}/view', 'ProductsController@view')->name('products.view');
    Route::get('products/create', 'ProductsController@create')->name('products.create');
    Route::get('store/{slug}', 'ProductsController@store')->name('products.store');

    Route::get('products/categories', 'ProductsController@categories')->name('products.categories');

    Route::get('products/settings', 'ProductsController@settings')->name('products.settings');

  // Orders
  Route::get('orders', 'OrdersController@index')->name('orders.all');
  Route::get('orders/view', 'OrdersController@view')->name('orders.view');
});

Route::get('/', 'HomeController@landing');
Route::get('/contact', 'HomeController@contact');
Route::get('/privacy', 'HomeController@privacy');
Route::get('/terms', 'HomeController@terms');

Route::get('users/all', 'UserController@all')->name('users.all');
Route::get('users/{user}/transfers', 'UserController@transfers')->name('users.transfers');
Route::get('users/{user}/transactions', 'UserController@transactions')->name('users.transactions');
Route::get('users/{user}/bills', 'UserController@bills')->name('users.bills');
Route::get('users/{user}', 'UserController@show')->name('users.show');

Route::get('test_upload', 'AccountController@test_upload')->name('test_upload');
Route::post('images-upload', 'AccountController@imagesUploadPost')->name('images.upload');

Route::any('success', function(){

    \DB::table('payment_logs')->where('id', 2)->update(array('status' => 1));

});
