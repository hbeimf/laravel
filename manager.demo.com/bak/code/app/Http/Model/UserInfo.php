<?php

namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;
use App\Http\Model\UserRelevantInfo;

class UserInfo extends Model {

    protected $table = 'bw_user_info';
    protected $primaryKey = 'uid';

    const USER_TYPE_NORMAL = 1;  //用户
    const USER_TYPE_AGENT = 2;  //代理
    const USER_TYPE_TRY = 3;  //试玩账户
    const USER_MIN_LENGTH = 4; //账号最小长度
    const USER_MAX_LENGTH = 15; //账号最大长度
    const PASS_MIN_LENGTH = 6; //密码最小长度
    const PASS_MAN_LENGTH = 14; //密码最小长度

    const STATUS3_YES = 1;  //未冻结
    const STATUS3_NO = 0;  //冻结
	
    protected $fillable = [
        'uid', 'parent_uid', 'promote_id', 'parent_uid_dir', 'register_url', 'hierarchy', 'nickname', 'real_name',
		'money_password', 'birthday', 'qq', 'wechat', 'mobile', 'note', 'level_id', 'register_ip',
		'login_time', 'login_num', 'created_at', 'updated_at', 'group_id', 'user_type', 'pic_id', 'sex',
		'status1', 'status2', 'status3', 'status4'
    ];

    // 获取前端登录用户信息
    public function getAdminInfo($uid) {
	$user = $this->where('uid', '=', $uid)->first()->toArray();
	return $user;
    }

    public function getUserStatus($uid) {
	$user = $this->where('uid', '=', $uid)->select('uid', 'status1', 'status2', 'status3', 'status4')->first()->toArray();
	return $user;
    }

    public function updateUser($update, $id) {
	return $this->where('uid', '=', $id)->update($update);
    }

    public function mcreate($aData) {
	if (!is_array($aData) || empty($aData)) {
	    return 0;
	}
	$aData['created_at'] = date('Y-m-d H:i:s', time());

	return (int) self::insert($aData);
    }

    public function getIsParent($uid) {
	return self::where('parent_uid', $uid)->count();
    }

    public function getComplete($uid, $select = ["*"]) {
	$res = self::where('u.id', $uid)
		->leftJoin('users as u', 'u.id', '=', 'bw_user_info.uid')
		->select($select)->first();
	if (is_object($res)) {
	    return $res->toArray();
	}
    }

    public function getBsComplete($uid, $select = ["*"]) {
	$res = self::where('u.id', $uid)
		->leftJoin('users as u', 'u.id', '=', 'bw_user_info.uid')
		->leftJoin('bw_user_relevant_info as b', 'b.uid', '=', 'u.id')
		->select($select)->first();
	if (is_object($res)) {
	    return $res->toArray();
	} else {
	    return [];
	}
    }

    /**
     * 
     * @param type $aWhere
     * @param type $page
     * @param type $size
     * @param type $selecdfields
     * @param type $aSort
     * @登录账号、昵称、用户分组、状态、类型、总资产、	上级账号、层级、VIP等级、提现次数、登录次数、新增时间
     */
    public function getListUserInfo(array $aWhere = array(), $page = 1, $size = 20, $aSelectedField = array(), array $aSort = []) {
	$select = "b.id, b.name,
	    bw_user_info.nickname,
	    bw_user_info.group_id,
	    bw_user_info.status1,
	    bw_user_info.status2,
	    bw_user_info.status3,
	    bw_user_info.status4,
	    bw_user_info.user_type,
	    c.total_money,
	    
	    bw_user_info.parent_uid,
	    bw_user_info.hierarchy,
	    bw_user_info.level_id,
	    bw_user_info.login_num,
	    c.withdraw_cash_time,
	    c.login_time,
	    b.created_at";
	//$table = UserInfo::selectRaw($select);

	if ($aWhere || $aSort) {
	    $table = $this->_getWhereBuilder($aWhere, $select, $aSort);
	    $count = $table->count();
	} else {
	    $table = UserInfo::selectRaw($select);
	    $table->leftJoin('users as b', 'b.id', '=', 'bw_user_info.uid')
		->leftJoin('bw_user_relevant_info as c', 'c.uid', '=', 'b.id');
	    $count = $table->orderBy('b.id', 'desc')->count();
	}

	$skip = ($page - 1) * $size;
	$list = $table->skip($skip)->limit($size)->get();

	$totalPage = ceil($count / $size);

	$data = [
	    'data' => $list->toArray(), // 当前页记录
	    'totalNum' => $count, // 记录条数
	    'currentPage' => $page, // 当前页
	    'totalPage' => $totalPage // 总页数
	];
	return $data;
    }

    public function _getWhereBuilder(array $aWhere, $fields = '*', array $aSort = []) {
	$oQueryBuilder = UserInfo::selectRaw($fields);
	$oQueryBuilder->leftJoin('users as b', 'b.id', '=', 'bw_user_info.uid')
	    ->leftJoin('bw_user_relevant_info as c', 'c.uid', '=', 'b.id');

	if (!$aSort) {
	    $aSort['id'] = 'DESC';
	}

	foreach ($aSort as $k => $v) {
	    if ($k == 'id') {
		$oQueryBuilder->orderBy('b.' . $k, $v);
		break;
	    }
	}
	foreach ($aWhere as $k => $v) {
	    if(!is_null($v)){
		switch ($k) {
		    case 'user_type'://用户类型
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'hierarchy'://层级
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'level_id'://VIP等级
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'status1'://1:启用， 0：禁用'
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'status2'://1:允许投注， 0：暂停投注'
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'status3'://1:解冻，0：冻节'
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'status4'://1:拉白，0：拉黑'
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'parent_uid'://上级uid
			$oQueryBuilder->where('bw_user_info.' . $k, $v);
			break;
		    case 'name':
			$oQueryBuilder->where('b.' . $k, 'like', '%' . $v . '%');
			break;
		}
	    }
	}

	return $oQueryBuilder;
    }

    /**
     * @所有上级
     * @param type $uid
     * @param type $pathList
     * @return type
     */
    public function get_top_parentid($uid, &$pathList = array()) {
	$user = self::where('uid', $uid)->leftJoin('users', 'users.id', '=', 'bw_user_info.uid')->first();
	if (is_object($user)) {
	    if ($user->hierarchy) {
		$pathList[] = array('uid' => $user->uid, 'hierarchy' => $user->hierarchy, 'name' => $user->name);
		$this->get_top_parentid($user->parent_uid, $pathList);
	    }
	}
	sort($pathList);
	return $pathList;
    }

    public function getUserLevel($where = '', $Fields = '', $order = '', $size = 20) {

	$page = $this->leftJoin('users', 'users.id', '=', 'uid')->where($where)->select('uid', 'name', 'parent_uid', 'nickname', 'hierarchy', 'level_id', 'user_type', 'status1')->orderBy('uid', 'DESC')->paginate($size);

	foreach ($page as $item) {
	    $_item = $item->toArray();
	    if ($item->childrens) {
		$_item['count'] = $item->childrens->count();
	    } else {
		$_item['count'] = 0;
	    }
	    if ($item->parents) {
		$_item['parent'] = $item->parents->name;
	    } else {
		$_item['parent'] = '';
	    }
	    if ($item->relevantInfo) {
		$_item['grow'] = $item->relevantInfo->grow;
	    } else {
		$_item['grow'] = 0;
	    }
	    if ($item->LevelConfig) {
		$_item['levelname'] = $item->LevelConfig->name;
	    } else {
		$_item['levelname'] = '';
	    }

	    $items[] = $_item;
	}

	$_page['data'] = &$items;
	$_page['totalNum'] = $page->total();
	$_page['currentPage'] = $page->currentPage();
	$_page['totalPage'] = ceil($page->total() / $size);

	return $_page;
    }

    public function user() {
	return $this->hasOne('App\Http\Model\Users', 'id', 'uid')->select('id', 'name');
    }

    public function childrens() {
	return $this->hasMany('App\Http\Model\UserInfo', 'parent_uid', 'uid');
    }

    public function parents() {
	return $this->hasOne('App\Http\Model\Users', 'id', 'parent_uid')->select('id', 'name');
    }

    public function relevantInfo() {
	return $this->hasOne('App\Http\Model\UserRelevantInfo', 'uid', 'uid')->select('uid', 'grow');
    }

    public function LevelConfig() {
	return $this->hasOne('App\Http\Model\LevelConfig', 'id', 'level_id');
    }

    /**
     *  根据父id获取用户信息
     * @param type $where
     * @param type $size
     * @return type
     */
    public function getUserByParentUid($where, $size) {
	$pageInfo = $this->selectRaw('bw_user_info.user_type as user_type, '
		. 'u.name as name,'
		. 'u.id as uid,'
		//. 'p.register_num as register_num,'
		. 'r.total_money as money,'
		. 'u.created_at as create_at')
	    ->leftJoin('users as u', 'u.id', '=', 'bw_user_info.uid')
	    //->leftJoin('bw_promote_config as p', 'p.uid', '=', 'bw_user_info.uid')
	    ->leftJoin('bw_user_relevant_info as r', 'r.uid', '=', 'bw_user_info.uid')
	    ->where($where)
	    ->groupBy('bw_user_info.id')
		->orderBy('bw_user_info.id', 'desc')
	    ->paginate($size)
	    ->toArray();
	return $pageInfo;
    }

    //获取当前用户的上级用户的路径，存放到arr里面
    protected $_parentArr = [];

    public function getParentArr($uid, $isArr = false) {
	$this->_getParentArr($uid);
	if ($isArr == false) {
	    foreach ($this->_parentArr as $val) {
		$_srt[] = $val['name'];
	    }
	    return implode('/', $_srt);
	}
	return $this->_parentArr;
    }

    protected function _getParentArr($uid) {
	if ($uid <= 0)
	    return;
	$user = $this->where("uid", '=', $uid)->first();
	if (!empty($user) && $user->parent_uid > 0) {
	    $this->_getParentArr(intval($user->parent_uid));
	}
	if ($user->user) {
	    $info = ['uid' => $user->uid, 'name' => $user->user->name];
	}
	$this->_parentArr[] = $info;
	return true;
    }
	
	/**
	 * @todo 获取用户的状态
	 *		  当含有非登录控制的，其他三种，异常状态，都展示状态为：异常
	 *			如:
	 *			登录正常+暂停投注，此时状态是：异常
	 *			停用登录+冻结+拉黑+暂停投注，状态是：异常
	 * @return int 1:正常,0:锁定,2:异常
	 * **/
	public function getStatue() {
		return $this->status1 == 1 && $this->status2 == 1 && $this->status3 == 1 && $this->status4 == 1 ? 1 : 2;
	}
	
    public function UserRelevantInfo() {
        return $this->belongsTo(UserRelevantInfo::class, 'uid', 'uid');
    }


}
