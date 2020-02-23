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

/** ログイン */
Route::post('/login', 'UserController@login');
/** 残高照会 */
Route::post('/showMoney', 'UserController@showMoney');
/** 取引履歴 */
Route::post('/history', 'UserController@history');
/** 預入 */
Route::post('/getMoney', 'UserController@getMoney');
/** 振込 */
Route::post('/putMoney', 'UserController@putMoney');
