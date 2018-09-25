<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class UserRelevantInfo extends Model{
    protected $table = 'bw_user_relevant_info';
    protected $primaryKey = 'id';
    //protected $fillable = ['withdraw_cash_time', 'update_time', 'updated_at', 'total_money', 'grow', 'money']; //开启白名单字段
    
    public function updateUser($update, $uid) {
	    return $this->where('uid', '=', $uid)->update($update);
    }
    
    public function getUserRel($uid){
	return $this->where('uid', $uid)->count();
    }
}
