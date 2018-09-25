<?php

/*
 * 用户等级配置借口
 * 新增等级，这里最多只能设置10个等级，超过则不能新增了
 * 用户等级报表
 */

namespace App\Http\Controllers\Admin;

use App\Helper\Fn;
use App\Http\Model\CeilPoint;
use Illuminate\Http\Request;
use App\Http\Model\PromoteConfig;
use App\Http\Model\ReturnPoint;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Http\Model\LevelConfig;
use App\Http\Model\UserInfo;

class UserlevelController extends CommonController {

	/**
	 * 获取用户等级列表
	 *
	 * @SWG\Get(
	 *      path="/api/userlevel/list",
	 *      tags={"admin-用户等级配置-huang"},
	 *      summary="获取用户等级列表",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response="default",
	 *          description="返回一个数组",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 * 				       property="id",
	 * 				        type="number",
	 * 				        description="该配置项的id"
	 * 				   ),
	 * 					@SWG\Property(
	 * 					    property="icon",
	 * 					    type="string",
	 * 						description="图片保存的文件ID"
	 * 					 ),
	 * 					@SWG\Property(
	 * 					    property="iconPath",
	 * 					    type="string",
	 * 						description="该配置项对应的图标url地址"
	 * 					 ),
	 * 					@SWG\Property(
	 * 					   property="rank",
	 * 					   type="number",
	 * 					   description="等级排名"
	 *					),
	 *					@SWG\Property(
	 *						property="name",
	 *						type="string",
	 *						description="等级名称，主要用于展示"
	 *					),
	 *					@SWG\Property(
	 *						property="need_grow",
	 *						type="number",
	 *						description="升级需要的成长值"
	 *					),
	 *					@SWG\Property(
	 *						property="recharge_scale",
	 *						type="number",
	 *						description="充值兑换比例"
	 *					),
	 *					@SWG\Property(
	 *						property="up_award",
	 *						type="number",
	 *						description="晋级奖励"
	 *					),
	 *					@SWG\Property(
	 *						property="jump_award",
	 *						type="number",
	 *						description="跳级奖励"
	 *					),
	 *					@SWG\Property(
	 *						property="created_at",
	 *						type="timestamp",
	 *						description="创建时间，用于显示在后台"
	 *					),
	 *					@SWG\Property(
	 *						property="updated_at",
	 *						type="timestamp",
	 *						description="关系时间，用于显示在后台"
	 *					)
	 *              )
	 * 			)
	 *      )
	 * )
	 */
	public function index() {
		$Config = new LevelConfig();

		$list = $Config->orderBy('rank', 'ASC')->get();
		foreach ($list as $key=>$item){
			$_list[] = $item->getInfo();
		}
		return response()->json(['code' => $this->successStatus, 'success' => $_list], $this->successStatus);
	}

	/**
	 * 获取用户等级的详细信息
	 * 
	 *
	 * @SWG\Get(
	 *      path="/api/userlevel/details",
	 *      tags={"admin-用户等级配置-huang"},
	 *      summary="获取用户等级的详细信息",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="string",
	 *          description="必填，等级的ID，在/api/userlevel/list中获取",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="该等级的配置信息",
	 *          @SWG\Schema(
	 *              type="object",
	 *              @SWG\Property(
	 *                  property="id",
	 *                  type="number",
	 *                  description="该配置项的id"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="icon",
	 *                  type="string",
	 *                  description="配置对应的图片id"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="iconPath",
	 *                  type="string",
	 *                  description="该配置项对应的图标url地址"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="rank",
	 *                  type="number",
	 *                  description="等级排名"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="name",
	 *                  type="string",
	 *                  description="等级名称，主要用于展示"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="need_grow",
	 *                  type="number",
	 *                  description="升级需要的成长值"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="recharge_scale",
	 *                  type="number",
	 *                  description="充值兑换比例"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="up_award",
	 *                  type="number",
	 *                  description="晋级奖励"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="jump_award",
	 *                  type="number",
	 *                  description="跳级奖励"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="created_at",
	 *                  type="timestamp",
	 *                  description="创建时间，用于显示在后台"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="updated_at",
	 *                  type="timestamp",
	 *                  description="关系时间，用于显示在后台"
	 *              )
	 *      )
	 *   )
	 * )
	 */
	public function details(Request $request) {
		$this->log->debug("用户等级详情页面展示！！");
		$id = $request->get('id');
		if (empty($id)) {
			return response()->json(['code' => $this->errorStatus, 'error' => '请提交ID'], $this->successStatus);
		}
		$Config = new LevelConfig();
		
		$info = $Config->find($id);
		
		if (empty($info)) {
			return response()->json(['code' => $this->errorStatus, 'error' => '没有找到对应项目'], $this->successStatus);
		}
		$item = $info->getInfo();
		
		return response()->json(['code' => $this->successStatus, 'success' => $item], $this->successStatus);
	}

	/**
	 * 添加/修改 用户等级的操作，用户等级最多只可以添加十级
	 * 
	 * @SWG\Post(
	 *      path="/api/userlevel/save",
	 *      tags={"admin-用户等级配置-huang"},
	 *      summary="添加/修改 用户等级的操作，用户等级最多只可以添加十级",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="string",
	 *          description="非必填，如果没有填写的话，这个是一个新增操作，填写的话，是一个修改操作",
	 *          required=true,
	 *      ),
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="该等级的配置信息",
	 *          @SWG\Schema(
	 *              type="object",
	 *              @SWG\Property(
	 *                  property="id",
	 *                  type="number",
	 *                  description="该配置项的id"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="icon",
	 *                  type="string",
	 *                  description="该配置项对应的图标ID"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="iconPath",
	 *                  type="string",
	 *                  description="该配置项对应的图标url地址"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="rank",
	 *                  type="number",
	 *                  description="等级排名"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="name",
	 *                  type="string",
	 *                  description="等级名称，主要用于展示"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="need_grow",
	 *                  type="number",
	 *                  description="升级需要的成长值"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="recharge_scale",
	 *                  type="number",
	 *                  description="充值兑换比例"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="up_award",
	 *                  type="number",
	 *                  description="晋级奖励"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="jump_award",
	 *                  type="number",
	 *                  description="跳级奖励"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="created_at",
	 *                  type="timestamp",
	 *                  description="创建时间，用于显示在后台"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="updated_at",
	 *                  type="timestamp",
	 *                  description="关系时间，用于显示在后台"
	 *              )
	 *			)
	 *		)
	 * )
	 */
	public function save(Request $request) {
		$this->log->debug("用户等级详情修改！！");

		$rule = [
			'id' => 'integer',
			'name' => 'required|string|max:20',
			'icon' => 'required',
			'rank' => 'required|integer|between:1,10',
			'need_grow' => 'required|integer',
			'recharge_scale' => 'required|numeric|between:0.1,99.99',
			'up_award' => 'integer',
			'jump_award' => 'integer',
		];
		$messages = [
			'rank.integer' => '等级排名只能是1至10级，请填写1至10之间的数字,不能是负数！',
			'rank.between' => '等级排名只能是1至10级，请填写1至10之间的数字,不能是负数！',
			'need_grow.integer' => '升级需要的成长值必须是一个数字，不能填写其它文字！',
			'recharge_scale.numeric' => '充值兑换比例请填写数字,支持小数点！',
			'recharge_scale.between' => '充值兑换比例请填写数字,支持小数点,不能是负数！',
			'up_award.integer' => '晋级奖励应该是是数字而不是其他文字！',
			'jump_award.integer' => '跳级奖励只可以是数字1，2，3这样的数字，不可以带小数点哦！',
		];
		try {
			$this->validator = Validator::make($request->all(), $rule,$messages);
			$this->validatorFails();
		} catch (\Exception $e) {
			return response()->json(['code' => $this->errorStatus, 'error' => $e->getMessage()], $this->successStatus);
		}
		
		$id = $request->get('id');
		$Config = new LevelConfig();

		//判断是否提供了 ID 提供ID为修改，
		//没有提供为新建
		if ($id > 0) {
			$info = $Config->find($id);
			//没有找到要修改的目标
			if (empty($info)) {
				return response()->json(['code' => $this->errorStatus, 'error' => '没有找到对应项目'], $this->successStatus);
			}
		} else {
			$info = $Config;

			//如果说只有10个等级的话，那 $count超过10就不允许在添加了；
			$count = $Config->count();
			if ($count >= 10) {
				return response()->json(['code' => $this->errorStatus, 'error' => '不支持添加的等级数量超过10个！'], $this->successStatus);
			}
		}
		
		$info->icon = $request->input('icon');
		$info->name = $request->input('name');
		$info->rank = $request->input('rank', 1);
		//外加几个校验；
		if ($info->rank <= 0 || $info->rank > 10) {
			return response()->json(['code' => $this->errorStatus, 'error' => '不支持定义高于10或者低于10的等级！'], $this->successStatus);
		}
		//去重，等级不能加重复了
		$have_info = $Config->where("rank",$info->rank)->first();
		if($have_info && $have_info->id != $info->id){
			return response()->json(['code' => $this->errorStatus, 'error' => '该等级已经设置过了，请不要重复设置等级！'], $this->successStatus);
		}
		$info->need_grow = $request->input('need_grow', 0);
		$info->recharge_scale = $request->input('recharge_scale', 1);
		if ($info->recharge_scale <= 0.01 || $info->recharge_scale > 99.99) {
			return response()->json(['code' => $this->errorStatus, 'error' => '充值兑换比例值在0.01至99.99之前！'], $this->successStatus);
		}
		$info->up_award = $request->input('up_award', 0);
		$info->jump_award = $request->input('jump_award', 0);
		
		$back = $info->save();
		
		if ($back) {
			return response()->json(['code' => $this->successStatus, 'success' => $info->getInfo()], $this->successStatus);
		} else {
			return response()->json(['code' => $this->errorStatus, 'error' => '操作失败，请重试！'], $this->successStatus);
		}
	}

	/**
	 * 用户等级配置条目删除
	 * 
	 * @SWG\Post(
	 *      path="/api/userlevel/del",
	 *      tags={"admin-用户等级配置-huang"},
	 *      summary="删除用户等级配置",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="string",
	 *          description="必填",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="default",
	 *          @SWG\Schema(
	 *              type="string",
	 *              @SWG\Property(
	 *                  property="code",
	 *                  type="number",
	 *                  description="状态码，200表示正常，其他为非正常状态"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="msg",
	 *                  type="string",
	 *                  description="状态说明"
	 *              )
	 *      )
	 *   )
	 * )
	 */
	public function del(Request $request) {
		$id = $request->get('id');
		if ($id <= 0) {
			return response()->json(['code' => $this->errorStatus, 'error' => '请选择要删除的条目！'], $this->successStatus);
		}

		//初始化并且找到条目，删除掉
		$Config = new LevelConfig();
		$info = $Config->find($id);

		if ($info) {
			$back = $info->delete();
		}

		if ($back) {
			return response()->json(['code' => $this->successStatus, 'success' => ''], $this->successStatus);
		} else {
			return response()->json(['code' => $this->errorStatus, 'error' => '删除失败！'], $this->successStatus);
		}
	}
	
	/**
	 * 获取导航action
	 *
	 * @SWG\Get(
	 *      path="/api/userlevel/users",
	 *      tags={"admin-用户等级配置-huang"},
	 *      summary="获取用户等级报表",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="level",
	 *          type="integer",
	 *          description="等级id 从api/userlevel/list 接口获取",
	 *          required=false,
	 *      ),
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="type",
	 *          type="integer",
	 *          description="用户类型，1：会员， 2:代理， 3:试玩",
	 *          required=false,
	 *      ),
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="status",
	 *          type="integer",
	 *          description="用户状态 1:启用， 0：禁用",
	 *          required=false,
	 *      ),
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="用户名，支持模糊搜索",
	 *          required=false,
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
	 *          name="limit",
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
	 *          description="导航数组",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 * 				       property="uid",
	 * 				        type="number",
	 * 				        description="该用户的UID"
	 * 				   ),
	 * 					@SWG\Property(
	 * 					    property="name",
	 * 					    type="string",
	 * 						description="用户账号"
	 * 					 ),
	 * 					@SWG\Property(
	 * 					   property="nickname",
	 * 					   type="string",
	 * 					   description="用户昵称"
	 *					),
	 *					@SWG\Property(
	 *						property="user_type",
	 *						type="number",
	 *						description="用户类型，1：会员， 2:代理， 3:试玩"
	 *					),
	 *					@SWG\Property(
	 *						property="status",
	 *						type="number",
	 *						description="用户状态"
	 *					),
	 *					@SWG\Property(
	 *						property="count",
	 *						type="number",
	 *						description="下级用户总数"
	 *					),
	 *					@SWG\Property(
	 *						property="parent",
	 *						type="string",
	 *						description="直属代理"
	 *					),
	 *					@SWG\Property(
	 *						property="level_id",
	 *						type="number",
	 *						description="等级的ID"
	 *					),
	 *					@SWG\Property(
	 *						property="hierarchy",
	 *						type="number",
	 *						description="层级"
	 *					),
	 *					@SWG\Property(
	 *						property="grow",
	 *						type="number",
	 *						description="成长值"
	 *					)
	 *              )
	 *			)
	 *		)
	 * )
	 */
	public function users(Request $request) {
		$level = $request->get('level');
		$type = $request->get('type');
		$status = $request->get('status');
		$name = $request->get('name');  //模糊查询
		$limit = $request->get('limit');  //模糊查询
		$limit = intval($limit) > 0 ?intval($limit):20;
		
		$where = [];
		if($level>=1 && $level <=10){
			$where[] = ['level_id', intval($level)];
		}
		if($type>=1 && $type <=3){
			$where[] = ['user_type', intval($type)];
		}
		if($status!==null){
			$where[] = ['status1', intval($status)];
		}
		if(!empty($name)){
			$where[] = ['name', 'like',"%$name%"];
		}
		$UserInfo = new UserInfo();
		
		$page = $UserInfo->getUserLevel($where,'','', intval($limit));
		
		return response()->json(['code' => $this->successStatus, 'success' => $page], $this->successStatus);
	}

}
