<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CeilPoint extends Model {
	protected $table = 'bw_ceil_point';

	public $timestamps = true;
	
	public function SystemCeilPoint()
    {
        return $this->hasOne('App\Http\Model\SystemCeilPoint','game_id','game_id');
    }
	
	/**
	 * 获取返点配置
	 * 先判断用户是一级还是其他级别，如果是5级，返回false；
	 * 再获取游戏列表，得到游戏种类，和返点
	 * 如果是一级，返点为系统返点配置，如果是2-4级,max_point返点先查询得到用户自己的返点,再查询bw_ceil_point里面的配置
	 * 顺序是,max_point先取ceil_point,再去bw_return_point里面的用户自己部分
	 * 拼接成array，返回
	 * 
	 * 注意：如果，2-4级中，上一级没有给他们配置，那么返点为0；可以不传入数据库
	 * @param array $userpoints 用户已经配置的返点列表
	 * **/
	public function getPoint($uid,$userpoints=[]) {
		if($this->uid > 0){
			$uid = $this->uid;
		}
		if($uid <= 0) return false;
		$Users = new UserInfo();
		$user = $Users->find($uid);
//		print_r($user->toArray());

		//没有这个用户
		if(empty($user))			return false;
		//如果用户层级大于2
		$Games = new Game();
		$gamePoint = $Games->select('id','name','max_point')->where('status',1)->get();
		foreach ($gamePoint as $k=>$v){
			$_gamePoint[$v->id] = ['name'=>$v->name,'max_point'=>$v->max_point,'game_id'=>$v->id,'id'=>$v->id];
		}
		
		/**
		 * [
		 *	0 => [
		 *			'name'=> '游戏名称',  
		 *			'max_point' => 1.0,  //最大返点
		 *			'game_id'	=> 1
		 *		],
		 *  .....
		 * ]
		 * **/
		$CeilPoints = new CeilPoint();
		$points = $CeilPoints->select('game_id','max_point')->where('uid',$uid)->get()->toArray();
		if(count($points) > 0){
			foreach ($points as $point){
				$_MaxPoints[$point['game_id']] = $point['max_point'];
			}
		} else {
			$ReturnPoints = new ReturnPoint();
			$points = $ReturnPoints->select('game_id','point')->where([['uid','=',$uid],['promote_id','=',0]])->get()->toArray();
			if(count($points) > 0){
				foreach ($points as $point){
					$_MaxPoints[$point['game_id']] = $point['point'];
				}
			}else{
				$PromoteConfig = new PromoteConfig();
				$points = $PromoteConfig->where('key','=',$user->register_url)->first();
				if($points && $points->ReturnPoint){
					foreach ($points->ReturnPoint as $val){
						$_MaxPoints[$val->game_id] = $val->point;
					}
				}
			}
		}
		
		if(count($userpoints)>0){
			foreach ($userpoints as $usepoint){
				$user_point[$usepoint['game_id']] = $usepoint['point'];
			}
		}
		
//		print_r($list->toArray());
		foreach ($_gamePoint as $key=>$val){
			
			//如果max_points 已经设置了，那么表示 return_point 设置了，或者 ceil_point设置了
			if(isset($_MaxPoints)){
				if(isset($_MaxPoints[$key]) && $_MaxPoints[$key] > 0){
					$val['max_point'] = $_MaxPoints[$key];
				}else{
					$val['max_point'] = 0;
				}
			
			//如果没有设置,有两种情况,一种是 第一层用户,那么返点等于系统设置,
			//如果非第一层,那么表示上级没有给设置,返点数清0
			}elseif($user->parent_uid > 0){
				$val['max_point'] = 0;
			}
			
			if(isset($user_point[$key]) && $user_point[$key] > 0){
				$val['point'] = $user_point[$key];
			}else{
				$val['point'] = 0;
			}
			$_gamePoint[$key] = $val;
		}
		
		return $_gamePoint;
	}

	/**
     * 获取游戏种类及返点上限配置
     * */
    public function getGameMaxPoint($uid) {
        return DB::table('cc_ceil_point as cp')
            ->leftJoin('uu_game as game', 'cp.game_id', '=', 'game.id')
            ->where('cp.uid', $uid)
            ->select('game.id', 'game.name', 'cp.max_point')
            ->get();
    }
    public function updateUser($update, $id, $uid) {
	    return $this->where('id', '=', $id)->where('uid', $uid)->update($update);
    }

}
