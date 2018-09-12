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

//Dingo API
use Dingo\Api\Routing\Router;

$api = app(Router::class);
$api->version('v1', ['namespace' => 'App\\Http\\Controllers\\Admin\\V1'], function (Router $api) {

//    $api->group(['middleware' => 'auth:api'], function (Router $api){

	$api->get('test', 'TestController@listTest');
	$api->get('test/{id}', 'TestController@getTest');
	$api->post('test', 'TestController@createTest');
	$api->put('test/{id}', 'TestController@updateTest');
	$api->delete('test/{id}', 'TestController@destroy');

//    });

});

// Route::get('test', 'Admin\LogController@test');

Route::post('login', 'Api\UserController@login');
Route::post('register', 'Api\UserController@register');

Route::group(['middleware' => 'auth:api'], function () {
	Route::post('details', 'Api\UserController@details');
	Route::post('logout', 'Api\UserController@logout');

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
