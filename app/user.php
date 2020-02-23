<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    // モデルと関連しているテーブル
    protected $table = 'users';

    // テーブルの主キー
    protected $primaryKey = 'CUST_NO';

    // IDが自動増分されるか
    public $incrementing = false;

    // モデルのタイムスタンプを更新するかの指示
    public $timestamps = false;
    
}
