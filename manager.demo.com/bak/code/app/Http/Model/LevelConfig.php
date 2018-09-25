<?php

//用户等级模型
//主要是用户等级的配置和查询
//关联用户信息表 bw_user_info

namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class LevelConfig extends Model {

	protected $table = 'bw_level_config';
	protected $primaryKey = 'id';
	
	public function File(){
		return $this->hasOne('App\Http\Model\File','id','icon')->select('id','name','dirType');
	}

	/**
	 * @属于哪个VIP等级
	 * @return type
	 */
	public function getUserLevel($id) {
		$user = self::where('id', $id)->first();
		if (is_null($user)) {
			return '';
		} else {
			return $user->toArray()['name'];
		}
	}
	
	public function getInfo(){
		$item = $this->toArray();
		if($this->File){
			$item['iconPath'] = $this->File->getUrl();
		}else{
			$item['iconPath'] = '';
		}
		return $item;
	}
	
	/**
	 * @所有VIP等级
	 * @param type $selecdfields
	 * @return type
	 */
	public function getLevelConfig($selecdfields){
	    $item = $this->select($selecdfields)->get();
	    if(is_object($item)){
		return $item->toArray();
	    }else{
		return [];
	    }
	}
}
