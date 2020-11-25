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

// Route::get('test', 'TestController@test');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('upload', 'MediaController@upload')->name('media.upload');

Route::prefix('v1')->group(function () {
	Route::get('charts/bills_paid_amount', 'ChartsController@billsPaidAmount');
	Route::get('charts/bills_paid_count', 'ChartsController@billsPaidCount');
	Route::get('charts/bills_count', 'ChartsController@billsCount');

	Route::get('users/{user}/stats', 'UserController@stats');

	Route::post('bills/create', 'BillController@store');
	Route::post('bills/create/wordpress', 'BillController@wordpress');
	Route::put('bills/{bill}/cancel', 'BillController@cancel');
	Route::put('bills/{bill}/timeout', 'BillController@timeout');
	Route::get('bills/{bill}', 'BillController@show');

    Route::post('fandaqah-register', 'UserController@register');
    Route::post('fandaqah-update-redirect', 'UserController@updateRedirect');
});
