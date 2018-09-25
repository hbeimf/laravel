<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class AgentReturn extends Model
{

    protected $table ='bw_forbid_return_point';

    protected $fillable = [
        'uid','note'
    ];

    public function userInfo() {
        return $this->belongsTo('App\Http\Model\UserInfo', 'uid', 'uid')->select('uid','nickname');
    }


}
