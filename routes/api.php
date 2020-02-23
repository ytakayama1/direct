<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/** ログイン */
Route::post('/login', 'UserController@login');
/** 残高照会 */
Route::post('/showMoney', 'UserController@showMoney');
/** 取引履歴 */
Route::post('/history', 'UserController@history');
/** 預入 */
Route::post('/stockMoney', 'UserController@stockMoney');
/** 振込 */
Route::post('/sendMoney', 'UserController@sendMoney');