<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['namespace' => 'api'], function () {
// 	Route::post('/login', 'UserController@login');
// });
// Route::group(['middleware' => 'auth:api', 'namespace' => 'api'], function () {
// 	Route::get('V1/test/passport', 'UserController@passport');
// });

Route::post('login', 'api\UserController@login');
Route::post('register', 'api\UserController@register');

Route::group(['middleware' => 'auth:api'], function () {
	Route::post('get-details', 'API\PassportController@getDetails');
});
