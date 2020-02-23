<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * ログインアクション
     * 
     * @param Request
     * @return Response
     * 
     */
    public function login(Request $request){

        Log::INFO('ログインアクション実行');
        
        /* リクエスト取得 */
        // リクエストからお客様番号、パスワードを取得
        $custNo = $request->input('custNo');
        $password = $request->input('password');
        Log::INFO('お客様番号：' . $custNo);
        
        /* DB問合せ */
        // DBからリクエストから取得したお客様番号のパスワードを取得
        $user = \App\User::find($custNo);

        /* 業務ロジック */        
        // 両者のパスワードを比較
        if($password != $user->PASSWORD){
            Log::INFO('ログイン失敗：パスワード不一致');
            // パスワード不一致
            return "NG";
        }

        // パスワード一致
        Log::INFO('ログイン成功');

        /* レスポンス作成 */
        return "OK";
    }

    /**
     * 残高照会アクション
     * 
     * @param Request
     * @return Response
     * 
     */
    public function showMoney(Request $request){

        Log::INFO('残高照会アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号を取得
        $custNo = $request->input('custNo');
        Log::INFO('お客様番号：' . $custNo);

        /* DB問合せ */
        // 取得したお客様番号の残高をDBから取得
        $user = \App\User::find($custNo);

        /* 業務ロジック */

        /* レスポンス作成 */

        return $user->AMOUNT;
    }

    /**
     * 取引履歴照会アクション
     * 
     * @param Request
     * @return Response
     * 
     */
    public function history(Request $request){

        Log::INFO('取引履歴照会アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号を取得
        $custNo = $request->input('custNo');
        Log::INFO('お客様番号：' . $custNo);

        /* DB問合せ */
        // 取得したお客様番号の取引履歴をDBから取得
        $histories = \App\History::where('CUST_NO', $custNo)
        ->get();

        /* 業務ロジック */

        /* レスポンス作成 */
        return $histories;
    }

    /**
     * 預入アクション
     * 
     * @param Request
     * @return Response
     * 
     */
    public function stockMoney(Request $request){

        Log::INFO('預入アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号、預入額を取得
        $custNo = $request->input('custNo');
        $stockAmount = $request->input('stockAmount');
        Log::INFO('お客様番号：' . $custNo);
        Log::INFO('預入額：' . $stockAmount);

        /* DB更新 */
        // DBの残高を取得
        $user = \App\User::find($custNo);
        $stock = $user->AMOUNT;
        Log::DEBUG('預入前残高：' . $stock);

        // DBの残高を更新
        $user->AMOUNT = $stock + $stockAmount;
        $user->save();
        Log::DEBUG('預入後残高：' . $user->AMOUNT);

        /* 業務ロジック */

        /* レスポンス作成 */

        return $user->AMOUNT;
    }

    /**
     * 振込アクション
     * 
     * @param Request
     * @return Response
     * 
     */
    public function sendMoney(Request $request){

        Log::INFO('振込アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号、振込額、振込先を取得
        $custNo = $request->input('custNo');
        $sendAmount = $request->input('sendAmount');
        $sendCustNo = $request->input('sendCustNo');
        Log::INFO('お客様番号：' . $custNo);
        Log::INFO('振込額：' . $sendAmount);
        Log::INFO('振込先：' . $sendCustNo);

        /* DB更新 */
        // 振込元のDBの残高を更新
        $user = \App\User::find($custNo);
        $user->AMOUNT = $user->AMOUNT - $sendAmount;
        $user->save();
        Log::INFO('振込元DB更新完了');
        Log::DEBUG('振込元の振込後残高：' . $user->AMOUNT);

        // 振込先のDBの残高を取得
        $sendUser = \App\User::find($sendCustNo);
        $sendUserAmount = $sendUser->AMOUNT;
        Log::DEBUG('振込先の残高：' . $sendUserAmount);

        // 振込先のDBの残高を更新
        $sendUser = \App\User::find($sendCustNo);
        $sendUser->AMOUNT = $sendUserAmount + $sendAmount;
        $sendUser->save();
        Log::INFO('振込先DB更新完了');
        Log::DEBUG('振込先の振込後残高：' . $sendUser->AMOUNT);

        /* 業務ロジック */

        /* レスポンス作成 */

        return $user->AMOUNT;
    }

}
