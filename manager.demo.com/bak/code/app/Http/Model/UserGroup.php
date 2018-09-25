<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {
    protected $table = 'bw_user_group';
    
    /**
     * @属于哪个分组
     * @return type
     */
    public function getUserGroup($id){
	$user = self::where('id', $id)->first();
	if(is_null($user)){
	    return '';
	}else{
	    return $user->toArray()['name'];
	}
    }

    protected $fillable = [
        'name',
        'begin_time',
        'end_time',
        'theme_limit',
        'deposit_time',
        'total_deposit_money',
        'max_deposit_money',
        'max_deposit_money',
        'withdraw_time',
        'withdraw_money',
        'note',
        'is_auto_group',
    ];

    public function userInfo() {
        return $this->belongsTo('App\Http\Model\UserInfo', 'uid', 'uid')->select('uid','nickname');
    }

}
