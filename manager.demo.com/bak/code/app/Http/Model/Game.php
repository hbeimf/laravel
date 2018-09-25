<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Game extends Model {
	protected $table = 'bw_game';

	public $timestamps = true;

    const STATUS_YES = 1; //启用
    const STATUS_NO = 2; //禁用

    public function getUserPoint(){
	$list = self::where('status', 1)->get(['id', 'name', 'max_point']);
	if(is_null($list)){
	    return [];
	}else{
	    $list = $list->toArray();
	}	
	return $list;
    }
    
    public function getGameReturnPoint($uid, $select = ['*']){
	$res = self::where('bw_game.status', self::STATUS_YES)->where('r.uid', $uid)
		->rightJoin('bw_return_point as r', 'r.game_id', '=', 'bw_game.id')
		->leftJoin('bw_ceil_point as c', 'c.uid', '=', 'r.uid')
		->select($select)->groupBy('r.game_id')->get();
	if(is_object($res)){
	    return $res->toArray();
	} else {
	    return [];
	}
    }
    
    /**
     * @获取返点数
     * @param type $uid
     */
    public function getUseGamePoint($uid){
	$userModel = new UserInfo();
	$ceilPointModel = new CeilPoint();
	$returnPointModel = new ReturnPoint();
	
	$gameList = self::where('status', self::STATUS_YES)->get(['id', 'name', 'max_point'])->toArray();
	$userInfo = $userModel->where('uid', $uid)->first();
	$gPoint = $ceilPointModel->where('uid',$userInfo->parent_uid)->get()->toArray();

	if($userInfo && $userInfo->hierarchy == 2){
	    $parents = $userModel->getAdminInfo($userInfo->parent_uid);
	    $gmaxPoint =  $returnPointModel->where([['uid', $parents['uid']], ['promote_id',  $parents['promote_id']]])->get();
	}else{
	    if($userInfo->parent_uid > 0){
		$parents = $userModel->getAdminInfo($userInfo->parent_uid);
		$gmaxPoint =  $returnPointModel->where([['uid', $parents['parent_uid']], ['promote_id',  $parents['promote_id']]])->get();
	    }else{
		$gmaxPoint = [];
	    }
	}
	
	$returnPoint = $returnPointModel->where([['uid', $userInfo->parent_uid], ['promote_id', $userInfo->promote_id]])->get(); // 自身返点数
	$lowerUserInfo = $userModel->where('parent_uid', $userInfo->uid)->first();
	if(is_object($lowerUserInfo)){
	    $lowerReturnPoint = $returnPointModel->where([['uid', $lowerUserInfo->parent_uid], ['promote_id',  $lowerUserInfo->promote_id]])->get();//下级返点
	}else{
	    $lowerReturnPoint = [];
	}
	$ceilPoint = $ceilPointModel->where('uid', $userInfo->uid)->get(); // 上级代理给下级代理最大返点限制
	
	$aData = [];
	foreach ($gameList as $val) {
		$v['gid'] = $val['id'];
		$v['gname'] = $val['name'];
		$v['gmax_point'] = '';//上级返点数
		
		$v['uid'] = $uid;
		$v['rid'] = null;
		$v['point'] = 0;//自身返点
		$v['cid'] = null;
		$v['max_point'] = null;//下级返点上限
		$v['lowerPoint'] = null;//下级返点
	
		foreach($gmaxPoint as $gp){
			if($gp->game_id == $val['id']){
			    $v['gmax_point'] = $gp['point'];
			    break;
			}
		}

		foreach ($gPoint as $g){
		    if($g['game_id'] == $val['id'] && ($g['max_point'] != '')){
			$v['gmax_point'] = $g['max_point'];
			break;
		    }
		}
		
		if (count($ceilPoint) > 0) {
			foreach($ceilPoint as $ceilV) {
				if ($ceilV->game_id == $val['id']) {
					$v['cid'] = isset($ceilV->id) ? $ceilV->id : null;
					$v['max_point'] = $ceilV->max_point;
					break;
				}
			}
		}
		
		if ($userInfo->hierarchy == 1) {
			$returnPoint = $returnPointModel->where([['uid', $uid], ['promote_id', 0]])->get(); // 上级返点数
			$v['gmax_point'] = $val['max_point'];//上级返点数
		}

		foreach ($returnPoint as $rp) {
			if ($rp->game_id == $val['id']) {
				$v['rid'] = $rp->id;
				$v['point'] = $rp->point;
			}
		}
		
		foreach ($lowerReturnPoint as $y){
			if ($y->game_id == $val['id']) {
				$v['lowerPoint'] = $y->point;
			}
		}
		$aData[] = $v;
	}
	
	return $aData;
    }
}
