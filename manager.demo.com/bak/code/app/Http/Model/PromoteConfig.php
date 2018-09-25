<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PromoteConfig extends Model {
	protected $table = 'bw_promote_config';

    const IS_DEL_YES = 1; //已删除
    const IS_DEL_NO = 2; //未删除

    const ACCOUNT_TYPE_AGENT = 1;  //代理
    const ACCOUNT_TYPE_MEMBER = 2;  //会员

    const LINK_TYPE_ONSITE = 1;  //站内
    const LINK_TYPE_OUTSIDE = 2;  //站外

	public $timestamps = false;
	protected $fillable = ['uid', 'account_type', 'promote_url', 'addtime', 'updatetime', 'key']; //开启白名单字段
	
	public function user()
    {
        return $this->hasOne('App\Http\Model\Users','id','uid');
    }
	public function Manage()
    {
        return $this->hasOne('App\Http\Model\Users','id','manage_id');
    }
	public function userinfo()
    {
        return $this->hasOne('App\Http\Model\UserInfo','uid','uid');
    }
	public function CeilPoint()
    {
        return $this->hasMany('App\Http\Model\CeilPoint','uid','uid');
    }
	public function ReturnPoint()
    {
        return $this->hasMany('App\Http\Model\ReturnPoint','promote_id','id');
    }

	/**
	 * 获取推广链接列表
	 * */
	public function getList($where,$limit=20) {
		$back = $this;
		if(isset($where['ex'])){
			$whereEx = $where['ex'];
			unset($where['ex']);
			$back = $back->whereExists(function ($query) use($whereEx) {
                $query->select(DB::raw(1))
                      ->from('bw_return_point')
                      ->whereRaw('bw_return_point.promote_id = bw_promote_config.id')
					  ->whereBetWeen('point',[$whereEx['min'],$whereEx['max']])
					  ->where("game_id",$whereEx['game_id']);
            });
		}
        $back = $back->leftJoin('users', 'users.id', '=', 'bw_promote_config.uid')->select('bw_promote_config.*','users.name')->where($where)->orderBy("addtime","desc");
		
		$list = $back->paginate($limit);
		$games = Game::get();
		
		//取出所有游戏，列表就不会缺失游戏
		$_game=[];
		foreach ($games as $game){
			$_game[$game->id]= ['id'=>$game->id,'name'=>$game->name,'point'=>0];
		}
		
		foreach ($list as $key=>$item){
			
			$_item=$item->toArray();
			$_item['link'] = $item->getLink();
			
			$_item['addtime'] = date('Y-m-d H:i:s',$item->addtime);
			$_item['updatetime'] = date('Y-m-d H:i:s',$item->updatetime);
			
			if($item->userinfo && $item->userinfo->parents){
				$_item['pname'] = $item->userinfo->parents->name;
			}else{
				$_item['pname'] = '';
			}
			if($item->userinfo){
				$_item['hierarchy'] = $item->userinfo->hierarchy;
			} else {
				$_item['hierarchy'] = 0;
			}
			
			//查询出管理员的名称
			if($item->Manage){
				$_item['manage_name'] = $item->Manage->name;
			}else{
				$_item['manage_name'] = '';
			}
			
			//查询出每个用户的返点数
			$_item['points'] = $item->ReturnPoint->toArray();
			$_item['games'] = $_game;
			
			//把有游戏和返点数对应上
			foreach ($_item['points'] as $val){
				$_item['games'][$val['game_id']]['point'] = $val['point'];
			}
			$_item['games'] = array_merge($_item['games']);
			unset($_item['points']);
			$_list[] = $_item;
		}
		
		$_page['data'] = &$_list;
		$_page['totalNum'] = $list->total();
		$_page['currentPage'] = $list->currentPage();
		$_page['totalPage'] = ceil($list->total()/$limit);

		return $_page;
    }
	
	public function getLink() {
		return $this->promote_url."/register?key=".$this->key;
	}
	
	/**
	 * 返回邀请码
	 * @param type $accountType 账户类型，1会员，2代理，3试玩
	 * @return type
	 */
	public function getOnlyKey($accountType) {
		$first = $accountType == '1' ? '6' : '8';
		$second = mt_rand(0, 9999999);
		$secondStrLen = strlen(strval($second));
		$neetZero = $secondStrLen < 7 ? (7 - $secondStrLen) : 0;
		$zero = "";
		for ($i=0; $i< $neetZero ; $i++) {
			$zero .= "0";
		}
		$key = $first . $zero. strval($second);
		$exist = $this->selectRaw('id')->where('key', $key)->first();
		if (isset($exist->id)) { // key已经存在，重新生成
			return $this->getOnlyKey($accountType);
		}
		return $key;
	}

	/**
	 * 获取默认的推广链接
	 * */
	public function getDefaultKey($promoteUrl, $columns = ['*']) {
        return $this->where(['promote_url' => $promoteUrl, 'is_default' => 1])
            ->first(['key'], $columns);
    }
}
