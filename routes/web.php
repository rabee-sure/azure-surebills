<?php

use App\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
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

Route::any('mastercard-webhook', 'BillController@masterCardWebHookResponse')->name('webhook-success');

//TODO remove this route
Route::any('deser', function(){
    // $client = new Client();
    // $orderResponse = $client->get(config('payment.drivers.mastercard_iframe.api_base_url').'/order/UVSZ-Z6RW-CZEN',
    // ['auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')]]);

    // $orderBody = json_decode($orderResponse->getBody()->getContents(), false);
    // dd($orderBody);



    $arr = array (
        '3DSecure' =>
        array (
          'veResEnrolled' => 'N',
          'xid' => 'HS7DYyuWTF9UKrzXE3W/0JD55QQ=',
        ),
        '3DSecureId' => '6f6766f1-d6fd-4460-9287-d63526efbb4d',
        'customer' =>
        array (
          'firstName' => 'amr',
        ),
        'device' =>
        array (
          'browser' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
          'ipAddress' => '156.216.17.194',
        ),
        'gatewayEntryPoint' => 'CHECKOUT',
        'merchant' => 'TEST3000000330',
        'order' =>
        array (
          'amount' => 4.0,
          'chargeback' =>
          array (
            'amount' => 0,
            'currency' => 'SAR',
          ),
          'creationTime' => '2020-12-03T07:16:09.725Z',
          'currency' => 'SAR',
          'description' => 'Invoice number: 1000345',
          'id' => 'UVSZ-Z6RW-CZEN',
          'lastUpdatedTime' => '2020-12-03T07:16:09.738Z',
          'merchantAmount' => 4.0,
          'merchantCategoryCode' => '8999',
          'merchantCurrency' => 'SAR',
          'reference' => '1c3e9aff-7160-41b0-ac8f-7d642d25cb9f',
          'status' => 'FAILED',
          'totalAuthorizedAmount' => 0.0,
          'totalCapturedAmount' => 0.0,
          'totalRefundedAmount' => 0.0,
        ),
        'response' =>
        array (
          'gatewayCode' => 'BLOCKED',
        ),
        'result' => 'FAILURE',
        'risk' =>
        array (
          'response' =>
          array (
            'gatewayCode' => 'REJECTED',
            'review' =>
            array (
              'decision' => 'NOT_REQUIRED',
              'note' => NULL,
            ),
            'rule' =>
            array (
              0 =>
              array (
                'data' => 'NO_LIABILITY_SHIFT',
                'name' => 'MSO_3D_SECURE',
                'recommendation' => 'REJECT',
                'type' => 'MSO_RULE',
              ),
              1 =>
              array (
                'data' => '511111',
                'name' => 'MSO_BIN_RANGE',
                'recommendation' => 'NO_ACTION',
                'type' => 'MSO_RULE',
              ),
              2 =>
              array (
                'data' => '156.216.17.194',
                'name' => 'MSO_IP_ADDRESS_RANGE',
                'recommendation' => 'NO_ACTION',
                'type' => 'MSO_RULE',
              ),
              3 =>
              array (
                'data' => 'EGY',
                'name' => 'MSO_IP_COUNTRY',
                'recommendation' => 'NO_ACTION',
                'type' => 'MSO_RULE',
              ),
            ),
          ),
        ),
        'sourceOfFunds' =>
        array (
          'provided' =>
          array (
            'card' =>
            array (
              'brand' => 'MASTERCARD',
              'expiry' =>
              array (
                'month' => '4',
                'year' => '27',
              ),
              'fundingMethod' => 'DEBIT',
              'issuer' => 'FISERV SOLUTIONS, LLC',
              'nameOnCard' => 'amr',
              'number' => '511111xxxxxx1118',
              'scheme' => 'MASTERCARD',
              'storedOnFile' => 'NOT_STORED',
            ),
          ),
          'type' => 'CARD',
        ),
        'timeOfLastUpdate' => '2020-12-03T07:16:09.738Z',
        'timeOfRecord' => '2020-12-03T07:16:09.738Z',
        'transaction' =>
        array (
          'acquirer' =>
          array (
            'id' => 'RIYADBANK_S2I',
            'merchantId' => '3000000330',
          ),
          'amount' => 4.0,
          'currency' => 'SAR',
          'id' => '1',
          'source' => 'INTERNET',
          'stan' => '0',
          'type' => 'PAYMENT',
        ),
        'version' => '58',
    );
    $orderBody = json_decode(json_encode($arr), FALSE);
    // dd($orderBody);
    $orderResponseJson['id'] = $orderBody->id ?? $orderBody->order->id;
    $orderResponseJson['card']['bin'] = '';
    $orderResponseJson['card']['holder'] = $orderBody->sourceOfFunds->provided->card->nameOnCard;
    $orderResponseJson['card']['binCountry'] = '';
    $orderResponseJson['card']['expiryYear'] = $orderBody->sourceOfFunds->provided->card->expiry->year;
    $orderResponseJson['card']['expiryMonth'] = $orderBody->sourceOfFunds->provided->card->expiry->month;
    $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
    $orderResponseJson['result']['code'] = is_array($orderBody->transaction) ? $orderBody->transaction[0]->response->acquirerCode : $orderBody->transaction->response->acquirerCode;
    $orderResponseJson['result']['description'] = is_array($orderBody->transaction) ? $orderBody->transaction[0]->result : $orderBody->transaction->result;
    $orderResponseJson['paymentType'] = '';
    $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
    dd($orderResponseJson);

    // $test = 'a:15:{s:8:"3DSecure";a:2:{s:13:"veResEnrolled";s:1:"N";s:3:"xid";s:28:"Rl8zcmB3cc9giz4XVwhHI9CyVIs=";}s:10:"3DSecureId";s:36:"82e7ca92-1d47-4cd6-a6e6-ffd0c0aa3733";s:8:"customer";a:1:{s:9:"firstName";s:3:"amr";}s:6:"device";a:2:{s:7:"browser";s:115:"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36";s:9:"ipAddress";s:15:"156.204.235.114";}s:17:"gatewayEntryPoint";s:8:"CHECKOUT";s:8:"merchant";s:14:"TEST3000000330";s:5:"order";a:15:{s:6:"amount";d:4;s:10:"chargeback";a:2:{s:6:"amount";i:0;s:8:"currency";s:3:"SAR";}s:12:"creationTime";s:24:"2020-12-02T13:27:00.725Z";s:8:"currency";s:3:"SAR";s:11:"description";s:23:"Invoice number: 1000343";s:2:"id";s:14:"3Y2F-UQ6A-6G4P";s:15:"lastUpdatedTime";s:24:"2020-12-02T13:27:00.752Z";s:14:"merchantAmount";d:4;s:20:"merchantCategoryCode";s:4:"8999";s:16:"merchantCurrency";s:3:"SAR";s:9:"reference";s:36:"7983bcf0-b1f9-4da6-9492-5ed52bad7122";s:6:"status";s:6:"FAILED";s:21:"totalAuthorizedAmount";d:0;s:19:"totalCapturedAmount";d:0;s:19:"totalRefundedAmount";d:0;}s:8:"response";a:1:{s:11:"gatewayCode";s:7:"BLOCKED";}s:6:"result";s:7:"FAILURE";s:4:"risk";a:1:{s:8:"response";a:3:{s:11:"gatewayCode";s:8:"REJECTED";s:6:"review";a:2:{s:8:"decision";s:12:"NOT_REQUIRED";s:4:"note";N;}s:4:"rule";a:4:{i:0;a:4:{s:4:"data";s:18:"NO_LIABILITY_SHIFT";s:4:"name";s:13:"MSO_3D_SECURE";s:14:"recommendation";s:6:"REJECT";s:4:"type";s:8:"MSO_RULE";}i:1;a:4:{s:4:"data";s:6:"511111";s:4:"name";s:13:"MSO_BIN_RANGE";s:14:"recommendation";s:9:"NO_ACTION";s:4:"type";s:8:"MSO_RULE";}i:2;a:4:{s:4:"data";s:15:"156.204.235.114";s:4:"name";s:20:"MSO_IP_ADDRESS_RANGE";s:14:"recommendation";s:9:"NO_ACTION";s:4:"type";s:8:"MSO_RULE";}i:3;a:4:{s:4:"data";s:3:"EGY";s:4:"name";s:14:"MSO_IP_COUNTRY";s:14:"recommendation";s:9:"NO_ACTION";s:4:"type";s:8:"MSO_RULE";}}}}s:13:"sourceOfFunds";a:2:{s:8:"provided";a:1:{s:4:"card";a:8:{s:5:"brand";s:10:"MASTERCARD";s:6:"expiry";a:2:{s:5:"month";s:1:"4";s:4:"year";s:2:"27";}s:13:"fundingMethod";s:5:"DEBIT";s:6:"issuer";s:21:"FISERV SOLUTIONS, LLC";s:10:"nameOnCard";s:3:"amr";s:6:"number";s:16:"511111xxxxxx1118";s:6:"scheme";s:10:"MASTERCARD";s:12:"storedOnFile";s:10:"NOT_STORED";}}s:4:"type";s:4:"CARD";}s:16:"timeOfLastUpdate";s:24:"2020-12-02T13:27:00.752Z";s:12:"timeOfRecord";s:24:"2020-12-02T13:27:00.752Z";s:11:"transaction";a:7:{s:8:"acquirer";a:2:{s:2:"id";s:13:"RIYADBANK_S2I";s:10:"merchantId";s:10:"3000000330";}s:6:"amount";d:4;s:8:"currency";s:3:"SAR";s:2:"id";s:1:"1";s:6:"source";s:8:"INTERNET";s:4:"stan";s:1:"0";s:4:"type";s:7:"PAYMENT";}s:7:"version";s:2:"58";}';
    // var_dump((object)unserialize($test));
});

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
	Route::apiResource('applications', 'ApplicationController');
    Route::apiResource('channels.applications', 'ChannelApplicationController');
    Route::resource('channels', 'ChannelController');
    Route::resource('bills', 'BillController');

    Route::get('customers/search_by_name', 'CustomerController@searchByName')->name('customers.search_name');
	Route::get('customers/search_by_mobile', 'CustomerController@searchByMobile')->name('customers.search_mobile');

	Route::resource('customers', 'CustomerController');

	Route::get('statement', 'StatementController@index')->name('statement.index');
    Route::get('transfer', 'TransferController@index')->name('transfer.index');
    Route::post('transfers', 'TransferController@store');

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
