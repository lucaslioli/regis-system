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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Judgment routes
route::get('/judgments/create', 'JudgmentController@create')->name('judgments.create');

// Documents routes
Route::get('/documents/index', 'DocumentController@index')->name('documents.index');

// Queries routes
Route::get('/queries/index', 'QueryController@index')->name('queries.index');
Route::get('/queries/create', 'QueryController@create')->name('queries.create');
Route::get('/queries/search', 'QueryController@search')->name('queries.search');
Route::delete('/queries/{query}', 'QueryController@destroy')->name('queries.destroy');
Route::get('/queries/{query}/edit', 'QueryController@edit')->name('queries.edit');
Route::post('/queries/store', 'QueryController@store');
Route::put('/queries/{query}', 'QueryController@update');