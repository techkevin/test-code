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
    return redirect()->route('user.index');
});

Route::group(['prefix' => 'user'], function () {
    Route::name('user.index')->get('/', 'UserController@index');
    Route::name('user.store')->post('/store', 'UserController@store');
    Route::name('user.bulk-destroy')->delete('/bulk-destroy', 'UserController@bulkDestroy');
    Route::name('user.update')->post('/{id}/update', 'UserController@update');
    Route::name('user.destroy')->delete('/{id}/destroy', 'UserController@destroy');
    Route::name('user.list')->get('/list', 'UserController@getList');
    Route::name('user.categories')->get('/categories', 'UserController@getCategories');
    Route::name('user.hobbies')->get('/hobbies', 'UserController@getHobbies');
});
