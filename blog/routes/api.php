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

// Route::post('login', 'api\UserController@login');
// Route::post('register', 'api\UserController@register');

// Route::group(['middleware' => 'auth:api'], function () {
// 	Route::post('get-details', 'API\PassportController@getDetails');
// });

$api = app('Dingo\Api\Routing\Router');
// $api->version('v1', function ($api) {
// https://laravel-china.org/articles/5804/laravel54jwtdingoapi-building-restfulapi
// https://laravel-china.org/articles/5548/brief-summary-of-laravel-passport-api-certification
$api->version('v1', ['namespace' => 'App\\Http\\Controllers\\Api'], function ($api) {
	$api->post('login', 'UserController@login');
	$api->post('register', 'UserController@register');
	// $api->post('register', 'App\Http\Api\Auth\RegisterController@register');
	$api->group(['middleware' => 'auth:api'], function ($api) {
		// $api->get('logout', 'App\Http\Api\Auth\LoginController@logout');
		// $api->resource('user', 'App\Http\Api\UsersController');
		$api->get('/test', 'UserController@getDetails');
	});
	// $api->get('refresh', 'App\Http\Api\UsersController@refresh');
});
