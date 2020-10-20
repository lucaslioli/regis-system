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

// Users routes
Route::get('/users', 'UserController@index')->name('users.index');
Route::get('/users/search', 'UserController@search')->name('users.search');

// Judgment
route::get('/judgments/index', 'JudgmentController@index')->name('judgments.index');
route::get('/judgments/create', 'JudgmentController@create')->name('judgments.create');
Route::get('/judgments/search', 'JudgmentController@search')->name('judgments.search');
Route::get('/judgments/{judgment}/edit', 'JudgmentController@edit')->name('judgments.edit');
Route::delete('/judgments/{judgment}', 'JudgmentController@destroy')->name('judgments.destroy');

// Documents
Route::get('/documents/index', 'DocumentController@index')->name('documents.index');
Route::get('/documents/create', 'DocumentController@create')->name('documents.create');
Route::get('/documents/search', 'DocumentController@search')->name('documents.search');
Route::get('/documents/{document}', 'DocumentController@show')->name('documents.show');
Route::delete('/documents/{document}', 'DocumentController@destroy')->name('documents.destroy');
Route::post('/documents/store', 'DocumentController@store');
Route::post('/documents/upload', 'DocumentController@upload');

// Queries
Route::get('/queries/index', 'QueryController@index')->name('queries.index');
Route::get('/queries/create', 'QueryController@create')->name('queries.create');
Route::get('/queries/search', 'QueryController@search')->name('queries.search');
Route::get('/queries/{query}/edit', 'QueryController@edit')->name('queries.edit');
Route::delete('/queries/{query}', 'QueryController@destroy')->name('queries.destroy');
Route::post('/queries/store', 'QueryController@store');
Route::put('/queries/{query}', 'QueryController@update');

// Document - Query
Route::post('/queries/setDocuments', 'QueryController@attachDocuments');
Route::delete('/queries/detachDocument/{query}/{document}', 'QueryController@detachDocument')->name('queries.detachDocument');