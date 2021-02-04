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
})->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Users routes
Route::get('/users', 'UserController@index')->name('users.index');
Route::get('/users/search', 'UserController@search')->name('users.search');
Route::get('/users/skipQuery/{query}', 'UserController@skipQuery')->name('users.skipQuery');
Route::get('/users/{user}/makeAdmin', 'UserController@makeAdmin')->name('users.makeAdmin');
Route::get('/users/{user}/revokeAdmin', 'UserController@revokeAdmin')->name('users.revokeAdmin');
Route::delete('/user/detachQuery/{user}/{query}', 'UserController@detachQuery')->name('users.detachQuery');

// Judgment
route::get('/judgments/index', 'JudgmentController@index')->name('judgments.index');
route::get('/judgments/create', 'JudgmentController@create')->name('judgments.create');
Route::get('/judgments/search', 'JudgmentController@search')->name('judgments.search');
Route::get('/judgments/{judgment}/edit', 'JudgmentController@edit')->name('judgments.edit');
Route::delete('/judgments/{judgment}', 'JudgmentController@destroy')->name('judgments.destroy');
Route::post('/judgments/store', 'JudgmentController@store');
Route::put('/judgments/{judgment}', 'JudgmentController@update');

// Documents
Route::get('/documents/index', 'DocumentController@index')->name('documents.index');
Route::get('/documents/create', 'DocumentController@create')->name('documents.create');
Route::get('/documents/search', 'DocumentController@search')->name('documents.search');
Route::get('/documents/{document}', 'DocumentController@show')->name('documents.show');
Route::delete('/documents/{document}', 'DocumentController@destroy')->name('documents.destroy');
Route::post('/documents/store', 'DocumentController@store');
Route::post('/documents/storeFromPath', 'DocumentController@storeFromPath');
Route::post('/documents/upload', 'DocumentController@upload');

// Queries
Route::get('/queries/index', 'QueryController@index')->name('queries.index');
Route::get('/queries/create', 'QueryController@create')->name('queries.create');
Route::get('/queries/search', 'QueryController@search')->name('queries.search');
Route::get('/queries/qrels', 'QueryController@qrelsExport')->name('queries.qrels');
Route::get('/queries/{query}/edit', 'QueryController@edit')->name('queries.edit');
Route::delete('/queries/{query}/detachAll', 'QueryController@detachAll')->name('queries.detachAll');
Route::delete('/queries/{query}', 'QueryController@destroy')->name('queries.destroy');
Route::post('/queries/attachDocuments', 'QueryController@attachDocuments');
Route::post('/queries/{query}/attachDocumentById', 'QueryController@attachDocumentById');
Route::post('/queries/store', 'QueryController@store');
Route::put('/queries/{query}', 'QueryController@update');

// Document - Query
Route::post('/queries/attachDocuments', 'QueryController@attachDocuments');
Route::delete('/queries/detachDocument/{query}/{document}', 'QueryController@detachDocument')->name('queries.detachDocument');

// Tiebreak
Route::get('/tiebreaks/index', 'TiebreakController@index')->name('tiebreaks.index');
Route::get('/tiebreaks/search', 'TiebreakController@search')->name('tiebreaks.search');
Route::get('/tiebreaks/{query}/{document}/edit', 'TiebreakController@edit')->name('tiebreaks.edit');

// Solr
Route::get('/basicSearch', function () {
    return view('solr.basic_search');
})->name('basic_seach');
