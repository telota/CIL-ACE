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

/*Route::match    (['get', 'post'], '/',    function () {
    return '404: Please specifiy the page you would like to visit.';
});*/

Route::get('/', function () {
    return redirect('/ace');
});

Route::get('/ace/id/{id}', 'appController@uri');

Route::get('/ace', 'appController@initiate');

/*Route::get('/', function () {
    return view('welcome');
});*/
