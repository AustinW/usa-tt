<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('trampoline', 'DownloadController@trampoline');

Route::get('autofill', 'PdfController@autofill');

Route::get('all', 'ViewController@index');

Route::get('download/select', 'DownloadController@select');
Route::post('download/select', 'DownloadController@select');

Route::get('import', 'ImportController@index');
Route::post('import', 'ImportController@store');