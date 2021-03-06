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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Route::get      ('/documentation/{entity}/{resource?}', 'apiController@documentation');

Route::match    (['get', 'post'], '/', function () {
    return '404: Please specifiy the resource you are looking for.';
});
Route::match    (['get', 'post'], '/{entity}/{id?}',    'apiController@select');
