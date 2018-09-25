<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Model\UserInfo;
use App\Http\Model\UserGroup;
use App\Http\Model\LevelConfig;
use App\Http\Model\UserRelevantInfo;
use App\Http\Model\CeilPoint;
use App\Http\Model\ReturnPoint;
use App\Http\Model\Game;
use App\Http\Model\Users;
use App\Http\Model\StatusLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class ClientController extends CommonController {

    public function __construct() {
	parent::__construct();
	$this->users = new Users();
	$this->userInfo = new UserInfo();
    }
    
    protected function getUser() {
	return Auth::user();
    }

    /**
     * @SWG\Post(
     *      path="/api/getUserList",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="用户列表 分页 搜索",
     *      description="请求该接口需要先登录。",
     *      operationId="getUserList",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="page",
     *          type="integer",
     *          description="default 1",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="size",
     *          type="integer",
     *          description="default 20",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[user_type]",
     *          type="integer",
     *          description="用户类型，1：会员， 2:代理， 3:试玩, 全部：不要传值过来",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[hierarchy]",
     *          type="integer",
     *          description="用户层级，1-5层",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[level_id]",
     *          type="integer",
     *          description="VIP等级，把level_id[id]传过来",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[parent_uid]",
     *          type="integer",
     *          description="下级的用户信息",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aSort[id]",
     *          type="integer",
     *          description="aSort[id]=>desc倒序，aSort[id]=>asc深序，默认倒序",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[status]",
     *          type="integer",
     *          description="用户状态status是个变量，有四种情况，aWhere[status1]1:启用， 0：禁用'， aWhere[status2]1:允许投注， 0：暂停投注， aWhere[status3]1:解冻，0：冻节'， aWhere[status4]1:拉白，0：拉黑'",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="aWhere[name]",
     *          type="string",
     *          description="用户账号",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     * 		  @SWG\Property(
     * 		   property="data",
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="int",
     *                      description="用户ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="登录账号"
     *                  ),
     * 			@SWG\Property(
     *                      property="nickname",
     *                      type="string",
     *                      description="昵称"
     *                  ),
     * 			 @SWG\Property(
     *                      property="group_id",
     *                      type="int",
     *                      description="用户分组"
     *                  ),
     * 			@SWG\Property(
     *                      property="status1",
     *                      type="int",
     *                      description="状态, 1：启用，0：禁用"
     *                  ),
     * 			@SWG\Property(
     *                      property="status2",
     *                      type="int",
     *                      description="1:允许投注， 0：暂停投注"
     *                  ),
     * 			@SWG\Property(
     *                      property="status3",
     *                      type="int",
     *                      description="1:解冻，0：冻节"
     *                  ),
     * 			@SWG\Property(
     *                      property="status4",
     *                      type="int",
     *                      description="1:拉白，0：拉黑"
     *                  ),
     * 			@SWG\Property(
     *                      property="user_type",
     *                      type="int",
     *                      description="类型：1：会员， 2:代理， 3:试玩"
     *                  ),
     *                  @SWG\Property(
     *                      property="total_money",
     *                      type="string",
     *                      description="总资产"
     *                  ),
     *                  @SWG\Property(
     *                      property="parent_uid",
     *                      type="string",
     *                      description="上级用户ID"
     *                  ),
     *			@SWG\Property(
     *                      property="parentName",
     *                      type="string",
     *                      description="上级账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="hierarchy",
     *                      type="int",
     *                      description="层级"
     *                  ),
     *                  @SWG\Property(
     *                      property="level_id",
     *                      type="integer",
     *                      description="VIP等级"
     *                  ),
     *                  @SWG\Property(
     *                      property="withdraw_cash_time",
     *                      type="int",
     *                      description="可提醒次数"
     *                  ),
     *			@SWG\Property(
     *                      property="login_time",
     *                      type="integer",
     *                      description="登录时间"
     *                  ),
     *                  @SWG\Property(
     *                      property="login_num",
     *                      type="integer",
     *                      description="登录次数"
     *                  ),
     *                  @SWG\Property(
     *                      property="created_at",
     *                      type="timestamp",
     *                      description="新增时间"
     *                  ),
     * 			@SWG\Property(
     *                      property="groupName",
     *                      type="string",
     *                      description="用户分组名称"
     *                  ),
     * 			@SWG\Property(
     *                      property="levelName",
     *                      type="string",
     *                      description="VIP等级名称"
     *                  ),
     * 			@SWG\Property(
     *                      property="lower",
     *                      type="integer",
     *                      description="下级"
     *                  ),
     *			@SWG\Property(
     *                      property="statusName",
     *                      type="string",
     *                      description="状态"
     *                  ),
     * 		  )
     *          ),
     * 		*	@SWG\Property(
     * 	    property="level_id",	
     * 	    type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="VIP等级"
     *                  ),
     * 		    )
     *          )
     *      )
     *   )
     * )
     */
    public function getUserList(Request $request) {
	$aWhere = $request->input('aWhere', []);
	$page = intval($request->input('page', 1));
	$size = intval($request->input('size', 20));
	$selecdfields = $request->input('filter', []);
	$aSort = $request->input('aSort', []);

	$list = $this->userInfo->getListUserInfo($aWhere, $page, $size, $selecdfields, $aSort);
	$g = new UserGroup();
	$level = new LevelConfig();
	$selecdfields = ['id', 'name'];
	$list['level_id'] = $level->getLevelConfig($selecdfields);

	foreach ($list['data'] as &$item) {
	    $obj = $this->users->getUser($item['parent_uid']);
	    $item['parentName'] = is_object($obj) ? $obj->name : '平台';
	    $item['groupName'] = $g->getUserGroup($item['group_id']); //用户分组
	    $item['levelName'] = $level->getUserLevel($item['level_id']); //VIP等级
	    $item['lower'] = $this->userInfo->getIsParent($item['id']); //下级
	    if($item['status1'] && $item['status2'] && $item['status3'] && $item['status4']){
		$item['statusName'] = '正常';
	    }else{
		$item['statusName'] = '异常';
	    }
	}

	return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/getUserLowerList",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="用户下级列表 分页",
     *      description="请求该接口需要先登录。",
     *      operationId="getUserLowerList",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="parent_uid",
     *          type="integer",
     *          description="aWhere[parent_uid] 账号ID",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="page",
     *          type="integer",
     *          description="default 1",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="size",
     *          type="integer",
     *          description="default 20",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="int",
     *                      description="用户ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="登录账号"
     *                  ),
     * 			@SWG\Property(
     *                      property="nickname",
     *                      type="string",
     *                      description="昵称"
     *                  ),
     * 			 @SWG\Property(
     *                      property="group_id",
     *                      type="int",
     *                      description="用户分组"
     *                  ),
     * 			@SWG\Property(
     *                      property="status1",
     *                      type="int",
     *                      description="状态, 1：启用，0：禁用"
     *                  ),
     * 			@SWG\Property(
     *                      property="status2",
     *                      type="int",
     *                      description="1:允许投注， 0：暂停投注"
     *                  ),
     * 			@SWG\Property(
     *                      property="status3",
     *                      type="int",
     *                      description="1:解冻，0：冻节"
     *                  ),
     * 			@SWG\Property(
     *                      property="status4",
     *                      type="int",
     *                      description="1:拉白，0：拉黑"
     *                  ),
     * 			@SWG\Property(
     *                      property="user_type",
     *                      type="int",
     *                      description="类型：1：会员， 2:代理， 3:试玩"
     *                  ),
     *                  @SWG\Property(
     *                      property="total_money",
     *                      type="string",
     *                      description="总资产"
     *                  ),
     *                  @SWG\Property(
     *                      property="parent_uid",
     *                      type="string",
     *                      description="上级账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="hierarchy",
     *                      type="int",
     *                      description="层级"
     *                  ),
     *                  @SWG\Property(
     *                      property="level_id",
     *                      type="integer",
     *                      description="VIP等级"
     *                  ),
     *                  @SWG\Property(
     *                      property="withdraw_cash_time",
     *                      type="int",
     *                      description="可提醒次数"
     *                  ),
     *                  @SWG\Property(
     *                      property="login_time",
     *                      type="integer",
     *                      description="登录次数"
     *                  ),
     *                  @SWG\Property(
     *                      property="created_at",
     *                      type="timestamp",
     *                      description="新增时间"
     *                  ),
     * 			@SWG\Property(
     *                      property="groupName",
     *                      type="string",
     *                      description="用户分组名称"
     *                  ),
     * 			@SWG\Property(
     *                      property="levelName",
     *                      type="string",
     *                      description="VIP等级名称"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function getUserLowerList(Request $request) {
	$aWhere = $request->input('aWhere', []);
	$page = intval($request->input('page', 1));
	$size = intval($request->input('size', 20));
	$selecdfields = $request->input('filter', []);
	$aSort = $request->input('aSort', []);

	if (isset($aWhere['parent_uid']) && $aWhere['parent_uid']) {
	    $list = $this->userInfo->getListUserInfo($aWhere, $page, $size, $selecdfields, $aSort);
	    $g = new UserGroup();
	    $level = new LevelConfig();
	    foreach ($list['data'] as &$item) {
		$item['groupName'] = $g->getUserGroup($item['group_id']); //用户分组
		$item['levelName'] = $level->getUserLevel($item['level_id']); //VIP等级
		$item['lower'] = $this->userInfo->getIsParent($item['id']); //下级
	    }
	} else {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => '用户ID不能为空'], $this->successStatus);
	}

	return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/upUserStatus",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="会员状态控制",
     *      description="请求该接口需要先登录。",
     *      operationId="upClientStatus",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="不能为空",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="status1",
     *          type="integer",
     *          description="1:启用， 0：禁用",
     *          required=false,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="status2",
     *          type="integer",
     *          description="1:允许投注， 0：暂停投注",
     *          required=false,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="status3",
     *          type="integer",
     *          description="1:解冻，0：冻节",
     *          required=false,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="status4",
     *          type="integer",
     *          description="1:拉白，0：拉黑",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     * 			@SWG\Property(
     *                      property="uid",
     *                      type="int",
     *                      description="账号id"
     *                  ),
     * 			@SWG\Property(
     *                      property="status1",
     *                      type="int",
     *                      description="1:启用， 0：禁用"
     *                  ),
     * 			 @SWG\Property(
     *                      property="status2",
     *                      type="int",
     *                      description="1:允许投注， 0：暂停投注"
     *                  ),
     * 			@SWG\Property(
     *                      property="status3",
     *                      type="int",
     *                      description="1:解冻，0：冻节"
     *                  ),
     * 			@SWG\Property(
     *                      property="status4",
     *                      type="int",
     *                      description="类型：1:拉白，0：拉黑'"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function upUserStatus(Request $request) {
	$this->log->debug("会员状态控制");

	$params = array_map("trim", [
	    'uid' => request('uid', 0),
	    'status1' => request('status1', -1),
	    'status2' => request('status2', -1),
	    'status3' => request('status3', -1),
	    'status4' => request('status4', -1)
	]);
	$this->log->debug(json_encode($params));
	foreach ($params as $key => $value) {
	    if ($params[$key] == -1) {
		unset($params[$key]);
	    }
	}

	if ($params['uid'] == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}

	if (count($params) < 2) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => '至少两个参数'], $this->successStatus);
	}

    //解冻清空缓存
	if($request->status3 == UserInfo::STATUS3_YES) {
        $loginCacheKey = config('cache-key.LOGIN_FAIL_NUM').$request->uid;
        if( cache()->has($loginCacheKey) ) {
            cache()->forget($loginCacheKey);
        }
    }
	$t = new UserInfo();
	$this->user = $this->getUser();
	$userStatus = $t->getUserStatus($params['uid']);
	$aData = [];	$i = 0;
	foreach ($userStatus as $k=>$v){
	    if($params[$k] != $v){
		$aData[$i]['status_type'] = substr($k, -1, 1);
		$aData[$i]['status_value'] = $v;
		$aData[$i]['uid'] = $params['uid'];
		$aData[$i]['manage_id'] = $this->user->id;
		$aData[$i]['msg'] = $this->user->name;
		$i++;
	    }
	}
	$reply = $t->updateUser($params, $params['uid']);
	if (intval($reply)) {
	    $success = $t->getUserStatus($params['uid']);
	    StatusLog::insert($aData);
	    return response()->json(['code' => $this->successStatus, 'success' => $success], $this->successStatus);
	}
	return response()->json(['code' => $this->errorStatus,
		'error' => '失败'], $this->successStatus);
    }
    
    /**
     * @SWG\Get(
     *      path="/api/getUserStatus",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="状态日志",
     *      description="请求该接口需要先登录。",
     *      operationId="getUserStatus",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="用户账号",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     * 		  @SWG\Property(
     * 		   property="data",
     *              type="array",
     *              @SWG\Items(
     * 			@SWG\Property(
     *                      property="status",
     *                      type="int",
     *                      description="状态"
     *                  ),
     * 			@SWG\Property(
     *                      property="msg",
     *                      type="string",
     *                      description="事由，如果msg的值不是正常2字，说明这个状态没有操作过"
     *                  ),
     *			@SWG\Property(
     *                      property="created_at",
     *                      type="string",
     *                      description="发生时间"
     *                  ),
     * 		  )
     *          ),
     *      )
     *   )
     * )
     */
    public function getUserStatus(Request $request){
	$this->log->debug("会员状态操作日志");
	$uid = request('uid', 0);

	if ($uid <= 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$t = new UserInfo();
	$s = new StatusLog();
	$tStatus = $t->getUserStatus($uid);
	$statusLog = $s->getStatusLog($uid);
	
	
	$newarray = [];
	$i = 0;
	if($tStatus){
	    unset($tStatus['uid']);
	    foreach ($tStatus as $k=>$v){
		$newarray[$k][$k] = $v;
		$newarray[$k]['msg'] = '正常';
		$newarray[$k]['created_at'] = '';
		$i++;
	    }
	}
	
	$list = [];
	foreach ($newarray as $k=>$y){
	    $list[$k] = $y;
	    foreach ($statusLog as $key=>$log){
		if($k == $key){
		    $list[$k] = $log;
		    break;
		}
	    }
	}

	$aData = [];
	foreach ($list as $k=>$z){
	    switch ($k){
		case 'status1':
		    $z['status'] = $z[$k] == 1 ? '启用' : '禁用';
		    break;
		case 'status2':
		    $z['status'] = $z[$k] == 1 ? '允许投注' : '暂停投注';
		    break;
		case 'status3':
		    $z['status'] = $z[$k] == 1 ? '解冻' : '冻节';
		    break;
		case 'status4':
		    $z['status'] = $z[$k] == 1 ? '拉白' : '拉黑';
		    break;
	    }
	    unset($z[$k]);
	    if($z['msg'] != '正常'){
		$z['msg'] .= $z['status'];
	    }
	    $aData[] = $z;
	}
	return response()->json(['code' => $this->successStatus, 'data' => $aData], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Get(
     *      path="/api/getUserGameList",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="新增用户",
     *      description="请求该接口需要先登录。",
     *      operationId="getUserGameList",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     * 			@SWG\Property(
     *                      property="gid",
     *                      type="integer",
     *                      description="游戏Id"
     *                  ),
     * 			@SWG\Property(
     *                      property="gname",
     *                      type="string",
     *                      description="游戏昵称"
     *                  ),
     * 			@SWG\Property(
     *                      property="gmax_point",
     *                      type="integer",
     *                      description="下级返点上限点数"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function getUserGameList(Request $request) {
	$g = new Game();
	$list = $g->getUserPoint();
	$aData = [];
	foreach ($list as $k => $v) {
	    $y['gid'] = $v['id'];
	    $y['gname'] = $v['name'];
	    $y['gmax_point'] = $v['max_point'];
	    $aData[$k] = $y;
	}
	return response()->json(['code' => $this->successStatus, 'success' => $aData], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/saveUserInfo",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="新增用户（提交）",
     *      description="请求该接口需要先登录。",
     *      operationId="saveUserInfo",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="账号需4-15个字母或数字的组合",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="密码需6-14个字母或数字的组合",
     *          required=true,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="user_type",
     *          type="integer",
     *          description="账号类型，1：会员， 2:代理， 3:试玩",
     *          required=true,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="mobile",
     *          type="integer",
     *          description="手机号码",
     *          required=false,
     *      ),
     * 	   @SWG\Parameter(
     *          in="formData",
     *          name="point",
     *          type="string",
     *          description="[['id' => 1, 'max_point' => 9, 'point' => 7], ['id' => 2, 'max_point' => 8, 'point' => 7]]	
     * 			    返点设置，id 是游戏ID， max_point 是系统换回给前端的默认最大值 point是设置的返点数",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="成功",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *          )
     *      )
     *   )
     * )
     */
    public function saveUserInfo(Request $request) {
	try{
	    $this->log->debug("创建用户和设置返点上限");
	    $rule = [
		'name' => 'required|between:6,20|alpha_dash_chinese|unique:users',
		'password' => 'required|min:6|alpha_dash',
		'c_password' => 'required|same:password',
		'point' => 'required',
	    ];
            $msg = [
		'name.between' => '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文',
		'name.alpha_dash_chinese' => '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文',
		'name.unique' => '该账号已存在',
                'password.min'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
                'password.alpha_dash'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
		'point.required' => '返点设置不能为空',
            ];
	    
	    $params = array_map("trim", [
		'name' => request('name', ''),
		'password' => request('password', ''),
		'c_password' => request('c_password', ''),
	    ]);

	    $this->log->debug(json_encode($params));

	    $info = [
		'user_type' => request('user_type', 1),
		'mobile' => request('mobile', ''),
	    ];

	    $gname = [
		'point' => request('point', []),
	    ];

	    if ($info['mobile']) {
		$rule['mobile'] = 'phone|unique:bw_user_info';
	    } elseif (empty($info['mobile'])) {
		unset($info['mobile']);
	    }
	    
	    $input = $request->all();
	    
            $this->validator = Validator::make($input, $rule, $msg);
            $this->validatorFails();
	    
	    foreach ($gname['point'] as $key => $val) {
		if(!isset($val['point']) || $val['point'] == ''){
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'请设置返点 0-'.$val['gmax_point']], $this->successStatus);
		}
		if ($val['point'] > $val['gmax_point']) {
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不能超过可设置返点范围 '.$val['gmax_point']], $this->successStatus);
		}
		if($val['point'] < 0){
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围  0-'.$val['gmax_point']], $this->successStatus);
		}
		
		if($info['user_type'] == 2 && !empty($val['max_point'])){//2是代理
		    if ($val['max_point'] > $val['point']) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'下级返点上限设置不能超过 '.$val['point']], $this->successStatus);
		    }
		    if ($val['max_point'] < 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'不在可设置返点范围 0-'.$val['point']], $this->successStatus);
		    }
		}
	    }

	    unset($params['c_password']);
	    $params['name'] = str_replace(' ', '', $params['name']);
	    $params['password'] = bcrypt(md5($params['password'])); //密码加密
	    
	    DB::transaction(function ()use($params, &$info, &$gname) {
		$uid = $this->users->mcreate($params);

		$info['uid'] = $uid;
		$this->userInfo->mcreate($info);

		$relInfo = [
		    'uid' => $uid,
		    'created_at' => date('Y-m-d H:i:s', time())
		];
		UserRelevantInfo::insert($relInfo);

		$point = [];
		foreach ($gname['point'] as $k => $v) {
		    $point[$k]['uid'] = $uid;
		    $point[$k]['game_id'] = $v['gid'];
		    $point[$k]['point'] = $v['point'];
		    $point[$k]['addtime'] = time();
		    if(isset($v['max_point']) && ($v['max_point'] != '')){
			$c = [
			    'uid' => $uid,
			    'game_id' => $v['gid'],
			    'max_point' => $v['max_point'],
			    'created_at' => date('Y-m-d H:i:s',time()),
			];
			CeilPoint::insert($c);
		    }
		}

		ReturnPoint::insert($point);
	    });
	    return response()->json(['code' => $this->successStatus], $this->successStatus);
	    
	} catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'error'  => $e->getMessage(),
            ];
        }
	
	return response()->json($this->result);
    }

    /**
     * @SWG\Post(
     *      path="/api/userBsInformation",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="用户基本资料",
     *      description="请求该接口需要先登录。",
     *      operationId="userBsInformation",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="用户ID",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     * 		@SWG\Schema(
     * 		    @SWG\Property(	
     * 			property="user",
     * 			type="array", 
     * 		@SWG\Items(
     * 		    @SWG\Property(
     *                      property="uid",
     *                      type="integer",
     *                      description="用户ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="登录账号"
     *                  ),
     * 			@SWG\Property(
     *                      property="user_type",
     *                      type="integer",
     *                      description="用户类型，1：会员， 2:代理， 3:试玩"
     *                  ),
     * 			 @SWG\Property(
     *                      property="withdraw_cash_time",
     *                      type="integer",
     *                      description="提现次数"
     *                  ),
     * 		    )
     * 		),
     * 	@SWG\Property(
     * 	    property="gameList",	
     * 	    type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="gid",
     *                      type="integer",
     *                      description="游戏ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="gname",
     *                      type="string",
     *                      description="游戏名称"
     *                  ),
     * 			    @SWG\Property(
     *                      property="gmax_point",
     *                      type="string",
     *                      description="可设置返点范围"
     *                  ),
     * 			    @SWG\Property(
     *                      property="rid",
     *                      type="integer",
     *                      description="返点设置ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="uid",
     *                      type="integer",
     *                      description="用户ID"
     *                  ),
     * 			     @SWG\Property(
     *                      property="point",
     *                      type="string",
     *                      description="返点设置"
     *                  ),
     * 			    @SWG\Property(
     *                      property="cid",
     *                      type="integer",
     *                      description="下级返点上限ID"
     *                  ),
     *			@SWG\Property(
     *                      property="lowerPoint",
     *                      type="string",
     *                      description="下级返点"
     *                  ),
     * 			@SWG\Property(
     *                      property="max_point",
     *                      type="string",
     *                      description="下级返点上限设置"
     *                  ),
     * 		    )
     *          )
     *      )
     *   )
     * )
     */
    public function userBsInformation(Request $request) {
	$uid = request('uid', 0);
	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$select = ['bw_user_info.uid', 'u.name', 'bw_user_info.user_type', 'b.withdraw_cash_time'];
	$list['user'] = $this->userInfo->getBsComplete($uid, $select);

	$g = new Game();
	$list['gameList'] = $g->getUseGamePoint($uid);
	
//	$select2 = ['bw_game.id as gid', 'bw_game.name as gname', 'bw_game.max_point as gmax_point', 'r.id as rid', 'r.uid', 'r.point', 'c.id as cid', 'c.max_point'];
//	$list['gameList'] = $g->getGameReturnPoint($uid, $select2);

	return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/getGameReturnPoint",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="返点查看",
     *      description="请求该接口需要先登录。",
     *      operationId="getGameReturnPoint",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="账号ID",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="gid",
     *                      type="integer",
     *                      description="游戏ID"
     *                  ),
     * 			@SWG\Property(
     *                      property="gname",
     *                      type="string",
     *                      description="游戏名称"
     *                  ),
     * 			    @SWG\Property(
     *                      property="gmax_point",
     *                      type="string",
     *                      description="可设置返点范围"
     *                  ),
     * 			@SWG\Property(
     *                      property="uid",
     *                      type="integer",
     *                      description="用户ID"
     *                  ),
     * 			     @SWG\Property(
     *                      property="point",
     *                      type="string",
     *                      description="返点设置"
     *                  ),
     * 			@SWG\Property(
     *                      property="max_point",
     *                      type="string",
     *                      description="下级返点上限设置"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function getGameReturnPoint(Request $request) {
	$uid = request('uid', 0);
	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$g = new Game();
	$list['gameList'] = $g->getUseGamePoint($uid);
//	$select2 = ['bw_game.id as gid', 'bw_game.name as gname', 'bw_game.max_point as gmax_point', 'r.id as rid', 'r.uid', 'r.point', 'c.id as cid', 'c.max_point'];
//	$list = $g->getGameReturnPoint($uid, $select2);

	return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }

    /**
     * @SWG\Post(
     *      path="/api/upGameReturnPoint",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="返点编辑保存",
     *      description="请求该接口需要先登录。",
     *      operationId="upGameReturnPoint",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="用户ID",
     *          required=true,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="point",
     *          type="string",
     *          description="[[rid’ => 1, ‘point’ => 9, ‘cid’ => 7, 'max_point'=> 6], [rid’ => 2, ‘point’ => 9, ‘cid’ => 7, 'max_point'=> 6]]
     * 		rid返点设置的ID，point返点设置，cid下级返点上限ID,max_point下级返点上限设置",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     * 		@SWG\Schema(
     *      )
     *   )
     * )
     */
    public function upGameReturnPoint(Request $request) {
	$this->log->debug("返点查看保存");
	$uid = request('uid', 0);
	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$gname = [
	    'point' => request('point', []),
	];
	$this->log->debug(json_encode($gname));

	foreach ($gname['point'] as $key => $val) {
	    if ($val['point'] > $val['gmax_point']) {
		return response()->json(['code' => $this->errorStatus,
			'error' => $val['gname'].'不能超过可设置返点范围'.$val['gmax_point']], $this->successStatus);
	    }
	    if($val['point'] < 0){
		return response()->json(['code' => $this->errorStatus,
			'error' => $val['gname'].'不在可设置返点范围  0-'.$val['gmax_point']], $this->successStatus);
	    }

	    if(!empty($val['max_point'])){//2是代理
		if ($val['max_point'] > $val['point']) {
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'下级返点上限设置不能超过 '.$val['point']], $this->successStatus);
		}
		if ($val['max_point'] < 0) {
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围 0-'.$val['point']], $this->successStatus);
		}
	    }
	    if(empty($val['max_point']) && (!empty($val['lowerPoint']))){
		if($val['point'] < $val['lowerPoint']){
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围'.$val['lowerPoint'].'-'.$val['gmax_point']], $this->successStatus);
		}
	    }
	    if($val['lowerPoint'] && ($val['max_point'] != '') && ($val['lowerPoint'] > $val['max_point'])){
		return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围'.$val['lowerPoint'].'-'.$val['point']], $this->successStatus);
	    }
	    if(empty($val['max_point']) && empty($val['lowerPoint']) && is_null($val['point'])){
		return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围 0-'.$val['gmax_point']], $this->successStatus);
	    }
	}

	DB::beginTransaction(); //开始事物

	$point = $relevant = [];
	$ret = new ReturnPoint();
	$cel = new CeilPoint();
	foreach ($gname['point'] as $v) {
	    $point['point'] = $v['point'];
	    $relevant['max_point'] = $v['max_point'];
	    $ret->updateUser($point, $v['rid']);

	    if (is_null($v['cid']) && ($v['max_point'] != '')) {//插入
		$c = [
		    'uid' => $uid,
		    'game_id' => $v['gid'],
		    'max_point' => $v['max_point'],
		    'created_at' => date('Y-m-d H:i:s',time()),
		];
		$cid = CeilPoint::insertGetId($c);
		if (!$cid) {
		    DB::rollback();
		    return response()->json(['code' => $this->errorStatus,
			    'error' => '提交返点设置上限失败!'], $this->successStatus);
		}
	    } elseif($v['cid']) {
		$cel->updateUser($relevant, $v['cid'], $uid);
	    }
	    
	}

	DB::commit();
	return response()->json(['code' => $this->successStatus], $this->successStatus);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/userCpInformation",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="完整资料",
     *      description="请求该接口需要先登录。",
     *      operationId="userCpInformation",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="不能为空",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     * 			@SWG\Property(
     *                      property="uid",
     *                      type="integer",
     *                      description="账号id"
     *                  ),
     * 			@SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="会员账号"
     *                  ),
     * 			 @SWG\Property(
     *                      property="real_name",
     *                      type="string",
     *                      description="真实姓名"
     *                  ),
     * 			@SWG\Property(
     *                      property="money_password",
     *                      type="string",
     *                      description="提款密码"
     *                  ),
     * 			@SWG\Property(
     *                      property="birthday",
     *                      type="string",
     *                      description="生日'"
     *                  ),
     * 			@SWG\Property(
     *                      property="qq",
     *                      type="string",
     *                      description="qq账号"
     *                  ),
     * 			 @SWG\Property(
     *                      property="wechat",
     *                      type="string",
     *                      description="微信号"
     *                  ),
     * 			@SWG\Property(
     *                      property="mobile",
     *                      type="string",
     *                      description="手机号"
     *                  ),
     * 			@SWG\Property(
     *                      property="note",
     *                      type="string",
     *                      description="备注'"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function userCpInformation(Request $request) {
	$uid = request('uid', 0);
	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$select = ['uid', 'u.name', 'real_name', 'money_password', 'birthday', 'qq', 'wechat', 'mobile', 'note'];
	$list = $this->userInfo->getComplete($uid, $select);
	if($list){
	    $list['money_password'] = '';
	}
	return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }

    /**
     * @SWG\Post(
     *      path="/api/saveUserBsInformation",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="提交保存用户基本资料",
     *      description="请求该接口需要先登录。",
     *      operationId="saveUserBsInformation",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="用户ID",
     *          required=true,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="user_type",
     *          type="integer",
     *          description="用户类型，1：会员， 2:代理",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="登录密码",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="c_password",
     *          type="string",
     *          description="确认密码",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_cash_time",
     *          type="integer",
     *          description="提现次数",
     *          required=true,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="point",
     *          type="string",
     *          description="[[rid’ => 1, ‘point’ => 9, ‘cid’ => 7, 'max_point'=> 6], [rid’ => 2, ‘point’ => 9, ‘cid’ => 7, 'max_point'=> 6]]
     * 		rid返点设置的ID，point返点设置，cid下级返点上限ID,max_point下级返点上限设置",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     * 		@SWG\Schema(
     *      )
     *   )
     * )
     */
    public function saveUserBsInformation(Request $request) {
	try{
	    $this->log->debug("更改基本资料");
	    $rule = [
		'withdraw_cash_time' => 'required',
		'point' => 'required',
	    ];
            $msg = [
		'withdraw_cash_time.required' => '可提现次数不能为空',
                'password.min'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
                'password.alpha_dash'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
		'point.required' => '返点设置不能为空',
            ];
	    
	    $uid = request('uid', 0);
	    $params = array_map("trim", [
		'password' => request('password', ''),
		'c_password' => request('c_password', ''),
	    ]);
	    if (empty($params['password']) || empty($params['c_password'])) {
		unset($params['password'], $params['c_password']);
	    } else {
		$rule['password'] = 'required|min:6|alpha_dash';
		$rule['c_password'] = 'required|same:password';
		unset($params['c_password']);
	    }
	    $this->log->debug(json_encode($params));
	    $info = [
		'user_type' => request('user_type', 1), //默认
	    ];
	    $rel = [
		'withdraw_cash_time' => request('withdraw_cash_time', 0),
	    ];
	    $gname = [
		'point' => request('point', []),
	    ];
	    
	    if(!preg_match('/^\d{1,2}$/', $rel['withdraw_cash_time'])){
		return response()->json(['code' => $this->errorStatus,
			'error' => '提现次数是数字，不能超过两位数'], $this->successStatus);
	    }
	    
	    if ($uid == 0) {
		return response()->json(['code' => $this->errorStatus,
			'error' => 'uid不能为空'], $this->successStatus);
	    }
	    
	    foreach ($gname['point'] as $key => $val) {
		if ($val['point'] > $val['gmax_point']) {
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不能超过可设置返点范围'.$val['gmax_point']], $this->successStatus);
		}
		if($val['point'] < 0){
		    return response()->json(['code' => $this->errorStatus,
			    'error' => $val['gname'].'不在可设置返点范围  0-'.$val['gmax_point']], $this->successStatus);
		}
		
		if($info['user_type'] == 2 && !empty($val['max_point'])){//2是代理
		    if ($val['max_point'] > $val['point']) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'下级返点上限设置不能超过 '.$val['point']], $this->successStatus);
		    }
		    if ($val['max_point'] < 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'不在可设置返点范围 0-'.$val['point']], $this->successStatus);
		    }
		}
		if(empty($val['max_point']) && (!empty($val['lowerPoint']))){
		    if($val['point'] < $val['lowerPoint']){
			return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'不在可设置返点范围'.$val['lowerPoint'].'-'.$val['gmax_point']], $this->successStatus);
		    }
		}
		if($val['lowerPoint'] && ($val['max_point'] != '') && ($val['lowerPoint'] > $val['max_point'])){
		    return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'不在可设置返点范围'.$val['lowerPoint'].'-'.$val['point']], $this->successStatus);
		}
		if(empty($val['max_point']) && empty($val['lowerPoint']) && is_null($val['point'])){
		    return response()->json(['code' => $this->errorStatus,
				'error' => $val['gname'].'不在可设置返点范围 0-'.$val['gmax_point']], $this->successStatus);
		}
	    }

	    if($info['user_type'] != 2){
		$userInfo = $this->userInfo->getAdminInfo($uid);
		if(isset($userInfo['user_type']) && $userInfo['user_type'] == 2){
		    return response()->json(['code' => $this->errorStatus,
				'error' => '代理不能修改'], $this->successStatus);
		}
	    }

	    DB::beginTransaction(); //开始事物
	    if (isset($params['password'])) {
		$params['password'] = bcrypt(md5($params['password'])); //密码加密
		$u = $this->users->updateUser($params, $uid);
		if (!$u) {
		    DB::rollback();
		    return response()->json(['code' => $this->errorStatus,
			    'error' => '密码格式不正确,至少6位字符，可包含数字,字母,下划线'], $this->successStatus);
		}
	    }
	    $ui = $this->userInfo->updateUser($info, $uid);
	    if (!$ui) {
		DB::rollback();
		return response()->json(['code' => $this->errorStatus,
			'error' => '提交类型失败'], $this->successStatus);
	    }

	    $r = new UserRelevantInfo();
	    $count = $r->getUserRel($uid);
	    if($count){
		$ur = $r->updateUser($rel, $uid);
		if (!$ur) {
		    DB::rollback();
		    return response()->json(['code' => $this->errorStatus,
			    'error' => '设置可体现次数'], $this->successStatus);
		}
	    }else{
		$rel['uid'] = $uid;
		$rel['created_at'] = date('Y-m-d H:i:s', time());
		$r->insert($rel);
	    }
	    $point = $relevant = [];
	    $ret = new ReturnPoint();
	    $cel = new CeilPoint();
	    foreach ($gname['point'] as $v) {
		$point['point'] = $v['point'];
		$relevant['max_point'] = $v['max_point'];
		$ret->updateUser($point, $v['rid']);
		if($info['user_type'] == 2){
		    if (is_null($v['cid']) && ($v['max_point'] != '')) {//插入
			$c = [
			    'uid' => $uid,
			    'game_id' => $v['gid'],
			    'max_point' => $v['max_point'],
			    'created_at' => date('Y-m-d H:i:s',time()),
			];
			$cid = CeilPoint::insertGetId($c);
			if (!$cid) {
			    DB::rollback();
			    return response()->json(['code' => $this->errorStatus,
				    'error' => '提交返点设置上限失败!'], $this->successStatus);
			}
		    } elseif($v['cid']) {
			$cel->updateUser($relevant, $v['cid'], $uid);
		    }
		}
	    }

	    DB::commit();
	    
	    $input = $request->all();
	    $this->validator = Validator::make($input, $rule, $msg);
            $this->validatorFails();
	    return response()->json(['code' => $this->successStatus], $this->successStatus);
	    
	} catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'error'  => $e->getMessage(),
            ];
        }
	return response()->json($this->result);
    }

    /**
     * 用户列表action
     *
     * @SWG\Post(
     *      path="/api/saveUserCpInformation",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="完整资料保存",
     *      description="请求该接口需要先登录。",
     *      operationId="saveUserCpInformation",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="账号ID",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="real_name",
     *          type="string",
     *          description="真实姓名",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="money_password",
     *          type="string",
     *          description="提款密码",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="birthday",
     *          type="string",
     *          description="生日",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="qq",
     *          type="string",
     *          description="QQ",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="wechat",
     *          type="string",
     *          description="微信",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="mobile",
     *          type="integer",
     *          description="手机号码",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="formData",
     *          name="note",
     *          type="string",
     *          description="备注",
     *          required=false,
     *      ),      
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *      )
     *   )
     * )
     */
    public function saveUserCpInformation(Request $request) {
	$this->log->debug("更改完整资料");
	$uid = request('uid', 0);

	$params = array_map("trim", [
	    'real_name' => request('real_name', ''),
	    'money_password' => request('money_password', ''),
	    'birthday' => request('birthday', ''),
	    'qq' => request('qq', ''),
	    'wechat' => request('wechat', ''),
	    'mobile' => request('mobile', ''),
	    'note' => request('note', ''),
	]);

	$this->log->debug(json_encode($params));

	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}

	if (empty($params['money_password'])) {
	    unset($params['money_password']);
	} else {
	    if(!preg_match('/^\d{6}$/', $params['money_password'])){
		return response()->json(['code' => $this->errorStatus,
			'error' => '请输入6个数字密码'], $this->successStatus);
	    }else{
		$params['money_password'] = bcrypt(md5($params['money_password']));
	    }
	}
	
	if (empty($params['mobile'])) {
	    unset($params['mobile']);
	} else {
	    if(!preg_match('/^1[34578][0-9]{9}$/', $params['mobile'])){
		return response()->json(['code' => $this->errorStatus,
			'error' => '请输入11位正确数字手机号码'], $this->successStatus);
	    }
	}
	
	$userInfo = $this->userInfo->updateUser($params, $uid);

	return response()->json(['code' => $this->successStatus], $this->successStatus);
    }

    public function relevantInfo(Request $request) {
	$uid = request('uid', 0);

	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
    }

    /**
     * 用户列表action
     *
     * @SWG\Get(
     *      path="/api/getUserHierarchy",
     *      tags={"admin-用户列表-JeromeRao"},
     *      summary="用户层级查看",
     *      description="请求该接口需要先登录。",
     *      operationId="getUserHierarchy",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="账号ID",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *			@SWG\Property(
     *                      property="uid",
     *                      type="integer",
     *                      description="yonh"
     *                  ),
     * 			@SWG\Property(
     *                      property="hierarchy",
     *                      type="integer",
     *                      description="层级"
     *                  ),
     *                  @SWG\Property(
     *                      property="nickname",
     *                      type="string",
     *                      description="账号昵称"
     *                  ),
     *          )
     *      )
     *   )
     * )
     */
    public function getUserHierarchy(Request $request) {
	$uid = request('uid', 0);

	if ($uid == 0) {
	    return response()->json(['code' => $this->errorStatus,
		    'error' => 'uid不能为空'], $this->successStatus);
	}
	$users = $this->userInfo->get_top_parentid($uid);

	return response()->json(['code' => $this->successStatus, 'success' => $users], $this->successStatus);
    }

}
