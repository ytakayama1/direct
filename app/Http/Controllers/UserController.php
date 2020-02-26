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
     *  custNo      お客様番号
     *  password    パスワード
     * 
     * @return Response
     *  result: success || false
     * 
     */
    public function login(Request $request){

        Log::INFO('ログインアクション実行');
        
        /* リクエスト取得 */
        // リクエストからお客様番号、パスワードを取得
        $custNo = $request->input('custNo');
        $password = $request->input('password');
        Log::INFO('お客様番号：' . $custNo);
        Log::DEBUG('パスワード：' . $password);
        // Log::DEBUG('リクエスト：' . $request);
        
        /* DB問合せ */
        // DBからリクエストから取得したお客様番号のパスワードを取得
        try{
            $user = \App\User::find($custNo);
        }catch(Exceptions $e){
            Log::ERROR('ERROR:' + $e);
        };

        /* 業務ロジック */    
        if($user == null){
            Log::DEBUG('お客様番号が存在しません');
            return response()->json([
                'result' => 'false'
                ]
            );
        }    
        // 両者のパスワードを比較
        if($password != $user->PASSWORD){
            Log::INFO('ログイン失敗：パスワード不一致');
            // パスワード不一致
            return response()->json([
                'result' => 'false'
                ]
            );
        }

        // パスワード一致
        Log::INFO('ログイン成功');
        return response()->json([
            'result' => 'success'
            ]
        );
    }

    /**
     * 残高照会アクション
     * 
     * @param Request
     *  custNo      お客様番号
     * 
     * @return Response
     *  CUST_NO: 
     *  NAME: 
     *  PASSWORD: 
     *  AMOUNT: 
     *  REGISTED_CUST_1: 
     *  REGISTED_CUST_2: 
     *  REGISTED_CUST_3: 
     *  REGISTED_CUST_4: 
     *  REGISTED_CUST_5: 
     * 
     */
    public function show(Request $request){

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
        return response()->json([
            'CUST_NO' => $user->CUST_NO, 
            'NAME' => $user->NAME, 
            'PASSWORD' => $user->PASSWORD,
            'AMOUNT' => $user->AMOUNT,
            'REGISTED_CUST_1' => $user->REGISTED_CUST_1, 
            'REGISTED_CUST_2' => $user->REGISTED_CUST_2, 
            'REGISTED_CUST_3' => $user->REGISTED_CUST_3, 
            'REGISTED_CUST_4' => $user->REGISTED_CUST_4, 
            'REGISTED_CUST_5' => $user->REGISTED_CUST_5, 
            ]
        );
    }

    /**
     * 取引履歴照会アクション
     * 
     * @param Request
     *  custNo      お客様番号
     * 
     * @return Response
     * [
     *  {
     *   "HISTORY_ID":"",
     *   "DATE":"",
     *   "DEPOSIT_AMOUNT":"",
     *   "STOCK_AMOUNT":"",
     *   "CUST_NO":""
     *  },
     * ]
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
        // Log::DEBUG('取引履歴：' + $histories);

        /* 業務ロジック */

        /* レスポンス作成 */
        // Log::DEBUG('json_encode($histories) : ' . json_encode($histories));
        return json_encode($histories);
    }

    /**
     * 振込アクション
     * 
     * @param Request
     *  custNo      お客様番号
     *  sendAmount     振込額
     *  sendCustNo      振込先お客様番号
     * 
     * @return Response
     * 
     */
    public function send(Request $request){

        Log::INFO('振込アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号、振込額、振込先を取得
        $custNo = $request->input('custNo');
        $sendAmount = $request->input('sendAmount');
        $sendCustNo = $request->input('sendCustNo');
        Log::INFO('お客様番号：' . $custNo);
        Log::DEBUG('振込額：' . $sendAmount);
        Log::DEBUG('振込先：' . $sendCustNo);

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
        // 振込元の取引履歴を追加
        $historyId = \App\History::all()
        ->max('HISTORY_ID');
        Log::DEBUG('振込元追加時のHISTORY_ID : ' . $historyId);

        $history = new \App\History;
        $history->HISTORY_ID = $historyId + 1;
        $history->DEPOSIT_AMOUNT = $sendAmount;
        $history->CUST_NO = $custNo;
        $history->save();
        Log::INFO('振込元取引履歴更新完了');

        // 振込先の取引履歴を追加
        $historyId = \App\History::all()
        ->max('HISTORY_ID');
        Log::DEBUG('振込先追加時のHISTORY_ID : ' . $historyId);

        $history = new \App\History;
        $history->HISTORY_ID = $historyId + 1;
        $history->STOCK_AMOUNT = $sendAmount;
        $history->CUST_NO = $sendCustNo;
        $history->save();
        Log::INFO('振込先取引履歴更新完了');

        /* レスポンス作成 */
        return $user->AMOUNT;
    }

    /**
     * 預入アクション
     * 
     * @param Request
     *  custNo      お客様番号
     *  stockAmount 預入額
     * 
     * @return Response
     * 
     */
    public function stock(Request $request){

        Log::INFO('預入アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号、預入額を取得
        $custNo = $request->input('custNo');
        $stockAmount = $request->input('stockAmount');
        Log::DEBUG('お客様番号：' . $custNo);
        Log::DEBUG('預入額：' . $stockAmount);

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
        // 取引履歴に追加
        $historyId = \App\History::all()
        ->max('HISTORY_ID');
        Log::DEBUG('HISTORY_ID : ' . $historyId);

        $history = new \App\History;
        $history->HISTORY_ID = $historyId + 1;
        $history->STOCK_AMOUNT = $stockAmount;
        $history->CUST_NO = $custNo;
        $history->save();
        Log::INFO('取引履歴更新完了');

        /* レスポンス作成 */
        return $user->AMOUNT;
    }

    /**
     * 登録済振込先情報取得アクション
     * 
     * @param Request
     * @return Response
     * {
     *  REGISTED_CUST_1: "",
     *  REGISTED_CUST_2: "",
     *  REGISTED_CUST_3: "",
     *  REGISTED_CUST_4: "",
     *  REGISTED_CUST_5: ""
     * }
     */
    public function getRegistedCust(Request $request){

        Log::INFO('登録済振込先情報取得アクション実行');

        /* リクエスト取得 */
        // リクエストからお客様番号を取得
        $custNo = $request->input('custNo');
        Log::DEBUG('お客様番号：' . $custNo);
        
        /* DB問合せ */
        // 登録済お客様番号を取得
        $user = \App\User::find($custNo);

        /* 業務ロジック */
        /* レスポンス作成 */
        return response()->json([
            'REGISTED_CUST_1' => $user->REGISTED_CUST_1, 
            'REGISTED_CUST_2' => $user->REGISTED_CUST_2, 
            'REGISTED_CUST_3' => $user->REGISTED_CUST_3, 
            'REGISTED_CUST_4' => $user->REGISTED_CUST_4, 
            'REGISTED_CUST_5' => $user->REGISTED_CUST_5, 
            ]
        );
    }
}
