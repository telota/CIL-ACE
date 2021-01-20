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

Route::match    (['get', 'post'], '/',    function () {
    return '404: Please specifiy the page you would like to visit.';
});

//Route::get ('/ace/js/{file}', 'appController@provideJS');
//Route::get ('/ace/css/{file}', function ($file) { return \File::get('/css/'.$file); });

Route::get ('/ace', 'appController@initiate');

/*Route::get('/', function () {
    return view('welcome');
});*/
