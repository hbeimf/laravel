<?php
/***
 * 用户信息表操作
 *
 * ***/
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Users extends Model {

	protected $table = 'users';
	protected $primaryKey = 'id';
	public $timestamps = true;

	public function UserInfo() {
		return $this->hasOne('App\Http\Model\UserInfo', 'uid', 'id');
	}
	
	public function getUser($id){
	    return $this->where('id', $id)->first();
	}

	public function getUserByName($name) {
		if (empty($name)) {
			return false;
		}
		return $this->where('name', $name)->first();
	}

	public function updateUser($update, $id) {
		return $this->where('id', '=', $id)->update($update);
	}

	public function mcreate($aData) {
		if (!is_array($aData) || empty($aData)) {
			return 0;
		}
		$aData['created_at'] = date('Y-m-d H:i:s', time());

		return (int) self::insertGetId($aData);
	}

	public function getUserCount($name) {
		$user = $this->where('name', '=', $name)->count();
		return $user;
	}

	public function existsUserById($id, $name = '') {
		$obj = $this->where('name', '=', $name)->where('id', '!=', $id)->first();
		return is_object($obj);
	}
}
