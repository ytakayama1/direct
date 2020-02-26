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
// Route::middleware('Cors')->post('/login', 'UserController@login');
Route::match(["get", "post", "options"], "/login", "UserController@login");
/** 残高照会 */
Route::post('/show', 'UserController@show');
/** 取引履歴 */
Route::post('/history', 'UserController@history');
/** 振込 */
Route::post('/send', 'UserController@send');
/** 預入 */
Route::post('/stock', 'UserController@stock');
/** 登録済振込先取得 */
Route::post('/registedCust', 'UserController@getRegistedCust');