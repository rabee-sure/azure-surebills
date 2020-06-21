<?php

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


Auth::routes();

Route::middleware(['auth'])->group(function () {
	Route::get('mobile_verify', 'MobileVerifyController@index')->name('mobile_verify');
	Route::post('mobile_verify', 'MobileVerifyController@store')->name('post.mobile_verify');
	Route::post('mobile_verify/resendCode', 'MobileVerifyController@resendCode')->name('resend_code');

	Route::get('account/account_information', 'AccountController@account_information')->name('account_information');
	Route::post('account-information', 'AccountController@storeAccountInformation')->name('account.information');

	Route::get('account/bank_information', 'AccountController@bank_information')->name('bank_information');
	Route::post('bank-information', 'AccountController@storeBankInformation')->name('bank.information');

	Route::get('account/business_information', 'AccountController@business_information')->name('business_information');
	Route::post('business-information', 'AccountController@storeBusinessInformation')->name('business.information');

	Route::get('account/change_password', 'AccountController@changePassword')->name('change_password');
	Route::post('change-password', 'AccountController@storeChangePassword')->name('change.password');

});

// py bill page
Route::get('/bills/{bill}/pay', 'BillController@pay')->name('paybillpage');

Route::middleware(['auth', 'mobile.verified'])->group(function () {
	Route::resource('bills', 'BillController');
	Route::resource('customers', 'CustomerController');

	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/', 'HomeController@index');
	Route::get('/integration', 'IntegrationController@index')->name('integration');
	Route::get('/integration/documentation', 'IntegrationController@documentation')->name('integration.documentation');
});
