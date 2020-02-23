<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    // モデルと関連しているテーブル
    protected $table = 'histories';

    // テーブルの主キー
    protected $primaryKey = 'HISTORY_ID';

    // IDが自動増分されるか
    public $incrementing = false;

    // モデルのタイムスタンプを更新するかの指示
    public $timestamps = false;
    
}
