<?php
// https://www.jianshu.com/p/2988ba405b3b?from=timeline&isappinstalled=0
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Model\AdminInfo;
use App\Http\Model\Roles;
use App\Http\Model\Users;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends CommonController {

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/adminRegister",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建管理员action",
	 *      description="请求该接口不需先登录。",
	 *      operationId="createMenu",
	 *      produces={"application/json"},
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="账号名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="email",
	 *          type="string",
	 *          description="邮箱",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="password",
	 *          type="string",
	 *          description="密码",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="c_password",
	 *          type="string",
	 *          description="重复密码",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="nickname",
	 *          type="string",
	 *          description="昵称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="img_id",
	 *          type="integer",
	 *          description="头像id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="status",
	 *          type="integer",
	 *          description="状态, 1:启用，0:禁用",
	 *          required=true,
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
	 *                      property="id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="int",
	 *                      description="uid"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="status",
	 *                      type="int",
	 *                      description="状态, 1：启用，0：禁用"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="email",
	 *                      type="string",
	 *                      description="email"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="nickname"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_url",
	 *                      type="string",
	 *                      description="img_url"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_id",
	 *                      type="string",
	 *                      description="img_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="updated_at",
	 *                      type="timestamp",
	 *                      description="更新时间"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function adminRegister(Request $request) {
		$this->log->debug("创建后台管理员");

		$params = array_map("trim", [
			'name' => request('name', ''),
			'email' => request('email', ''),
		]);

		$password = [
			'password' => request('password', ''),
			'c_password' => request('c_password', ''),
		];

		$info = array_map("trim", [
			'nickname' => request('nickname', ''),
			'img_id' => request('img_id', 0),
			'status' => request('status', -1),
		]);

		$roleId = request('role_id', 0);

		if ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '账号名称不能为空'], $this->successStatus);
		} elseif ($password['password'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '密码不能为空'], $this->successStatus);
		} elseif ($password['password'] != $password['c_password']) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '重复密码与密码不一致！'], $this->successStatus);
		} elseif ($info['status'] == -1) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '状态有误！'], $this->successStatus);
		}
		
		if ($password['password'] == '' || strlen($password['password']) < strlen('123456')) {
		    return response()->json(['code' => $this->errorStatus,
		        'error' => '密码限制最少6位！'], $this->successStatus);
		}

		if ($roleId == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色有误！'], $this->successStatus);
		}

		if ($params['email'] != '' && !$this->isEmail($params['email'])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '邮箱格式不正确'], $this->successStatus);
		}

		$r = User::selectRaw('id')->where('name', '=', $params['name'])->first();

		// 检查是否已被注册过了
		if (is_object($r)) {
			return response()->json(['code' => $this->errorStatus, 'error' => '账号已被注册！'], $this->successStatus);
		} else {
			unset($params['c_password']);
			$params['password'] = bcrypt($password['password']);
			$user = User::create($params);
			// 将账号与 Admin 关联
			$t = new AdminInfo();
			// var_dump($info['nickname']);exit;
			$adminInfo = $t->createAdmin($user->id, $info['nickname'], $info['img_id'], $info['status']);

			// 关联角色 role
			$tableRole = new Roles();
			$tableRole->attachRole($user->id, $roleId);

			$reply = $t->getAdminByUid($user->id);
			return response()->json(['code' => $this->successStatus, 'success' => $reply], $this->successStatus);
		}

	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/adminLogin",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="管理员登录action",
	 *      description="请求该接口不需先登录。",
	 *      operationId="createMenu",
	 *      produces={"application/json"},
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="email",
	 *          type="string",
	 *          description="邮箱",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="password",
	 *          type="string",
	 *          description="密码",
	 *          required=true,
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
	 *                      property="token",
	 *                      type="string",
	 *                      description="token"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function adminLogin() {
		$this->log->debug("管理员登录");

		$params = array_map("trim", [
			'email' => request('email', ''),
			'password' => request('password', ''),
		]);
		
		if ($params['password'] == '' || strlen($params['password']) < strlen('123456')) {
		    return response()->json(['code' => $this->errorStatus,
		        'error' => '密码限制最少6位！'], $this->successStatus);
		}

		if (Auth::attempt(['name' => request('email'), 'password' => request('password')])) {
			$user = Auth::user();
			// 检查账号是否被禁用
			$tableAdmin = new AdminInfo();
			$admin = $tableAdmin->getRowByUid($user->id);
			if ($admin['status'] == 0) {
				return response()->json(['code' => $this->errorStatus,
					'error' => '账号已被禁止登录'], $this->successStatus);
			}

			$success['token'] = $user->createToken('MyApp')->accessToken;
			return response()->json(['code' => $this->successStatus, 'success' => $success], $this->successStatus);
		} else {
			return response()->json(['code' => $this->errorStatus, 'error' => '账号密码错误'], $this->successStatus);
		}
	}

	private function isEmail($mail) {
		$checkmail = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
		if (preg_match($checkmail, $mail)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/adminUpdate",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="修改管理员action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="createMenu",
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
	 *          description="uid",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="账号名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="email",
	 *          type="string",
	 *          description="邮箱",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="nickname",
	 *          type="string",
	 *          description="昵称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="img_id",
	 *          type="integer",
	 *          description="头像id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="status",
	 *          type="integer",
	 *          description="状态, 1:启用，0:禁用",
	 *          required=true,
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
	 *                      property="id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="int",
	 *                      description="uid"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="status",
	 *                      type="int",
	 *                      description="状态, 1：启用，0：禁用"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="email",
	 *                      type="string",
	 *                      description="email"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="nickname"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_url",
	 *                      type="string",
	 *                      description="img_url"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_id",
	 *                      type="string",
	 *                      description="img_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="updated_at",
	 *                      type="timestamp",
	 *                      description="更新时间"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function adminUpdate() {
		$this->log->debug("修改后台管理员密码");

		$uid = request('uid', 0);

		$params = array_map("trim", [
			// 'uid' => request('uid', 0),
			'name' => request('name', ''),
			'email' => request('email', ''),
		]);

		// $password = [
		//      'password' => request('password', ''),
		//     'c_password' => request('c_password', ''),
		// ];

		$info = [
			'nickname' => request('nickname', ''),
			'img_id' => request('img_id', 0),
			'status' => request('status', -1),
		];

		$roleId = request('role_id', 0);
		// $this->log->debug(json_encode($params));

		if ($uid == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'uid不能为空'], $this->successStatus);
		} elseif ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '账号名称不能为空'], $this->successStatus);
		} elseif ($info['status'] == -1) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '状态有误！'], $this->successStatus);
		}

		if ($roleId == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '所属角色有误！'], $this->successStatus);
		}

		$t1 = new AdminInfo();

		$usersTable = new Users();
		if ($usersTable->existsUserById($uid, $params['name'])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '用户已存在，不能重复添加！!'], $this->successStatus);
		}

		$roleSlug = $t1->getRoleByUid($uid);
		if ($roleSlug == 'superadmin') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '超级管理员不支持此操作！!'], $this->successStatus);
		}

		// 关联角色 role
		$tableRole = new Roles();
		$tableRole->detachAllRoles($uid);
		$tableRole->attachRole($uid, $roleId);

		// $params['password'] = bcrypt($password['password']);
		$t = new User();
		$t->where('id', '=', $uid)->update($params);

		// $t1 = new AdminInfo();
		$t1->updateAdminInfo($uid, $info['nickname'], $info['img_id'], $info['status']);
		$reply = $t1->getAdminByUid($uid);

		return response()->json(['code' => $this->successStatus, 'success' => $reply], $this->successStatus);
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/adminUpdatePassword",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="修改管理员密码action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="adminUpdatePassword",
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
	 *          description="uid",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="password",
	 *          type="string",
	 *          description="password",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="c_password",
	 *          type="string",
	 *          description="c_password",
	 *          required=true,
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
	 *                      property="id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="int",
	 *                      description="uid"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="status",
	 *                      type="int",
	 *                      description="状态, 1：启用，0：禁用"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="email",
	 *                      type="string",
	 *                      description="email"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="nickname"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_url",
	 *                      type="string",
	 *                      description="img_url"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_id",
	 *                      type="string",
	 *                      description="img_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="updated_at",
	 *                      type="timestamp",
	 *                      description="更新时间"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function adminUpdatePassword() {
		$this->log->debug("修改后台管理员密码1");

		$uid = request('uid', 0);

		$password = [
			'password' => request('password', ''),
			'c_password' => request('c_password', ''),
		];

		$roleId = request('role_id', 0);
		// $this->log->debug(json_encode($params));

		if ($uid == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'uid不能为空'], $this->successStatus);
		} elseif ($password['password'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '密码不能为空'], $this->successStatus);
		} elseif ($password['password'] != $password['c_password']) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '两次密码输入不一致'], $this->successStatus);
		}

		$params = [
			'password' => bcrypt($password['password']),
		];
		$t = new User();
		$t->where('id', '=', $uid)->update($params);

		$t1 = new AdminInfo();
		$reply = $t1->getAdminByUid($uid);

		return response()->json(['code' => $this->successStatus, 'success' => $reply], $this->successStatus);
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/adminEnable",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="启用禁用管理员action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="adminEnable",
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
	 *          description="uid",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="status",
	 *          type="integer",
	 *          description="status, 1：启用， 0： 禁用",
	 *          required=true,
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
	 *                      property="id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="int",
	 *                      description="uid"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="status",
	 *                      type="int",
	 *                      description="状态, 1：启用，0：禁用"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="email",
	 *                      type="string",
	 *                      description="email"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="nickname"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_url",
	 *                      type="string",
	 *                      description="img_url"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_id",
	 *                      type="string",
	 *                      description="img_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="updated_at",
	 *                      type="timestamp",
	 *                      description="更新时间"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function adminEnable() {
		$this->log->debug("启用禁用管理员");

		$params = array_map("trim", [
			'uid' => request('uid', 0),
			'status' => request('status', -1),
		]);

		$this->log->debug(json_encode($params));

		if ($params['uid'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'uid不能为空'], $this->successStatus);
		} elseif ($params['status'] == -1) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '状态不能为空'], $this->successStatus);
		}

		if (!in_array($params['status'], [1, 0])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '状态有误'], $this->successStatus);
		}

		$t = new AdminInfo();
		$roleSlug = $t->getRoleByUid($params['uid']);
		if ($roleSlug == 'superadmin') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '超级管理员不支持此操作！'], $this->successStatus);
		} else {
			$t->updateStatus($params['uid'], $params['status']);
			$reply = $t->getAdminByUid($params['uid']);

			return response()->json(['code' => $this->successStatus, 'success' => $reply], $this->successStatus);
		}
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getAdmin",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取管理员 分页 action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="getAdmin",
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
	 *                      property="id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="int",
	 *                      description="uid"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="status",
	 *                      type="int",
	 *                      description="状态, 1：启用，0：禁用"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="email",
	 *                      type="string",
	 *                      description="email"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="nickname"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_url",
	 *                      type="string",
	 *                      description="img_url"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="img_id",
	 *                      type="integer",
	 *                      description="img_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="role_name",
	 *                      type="string",
	 *                      description="角色名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="role_id",
	 *                      type="integer",
	 *                      description="角色id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="updated_at",
	 *                      type="timestamp",
	 *                      description="更新时间"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function getAdmin() {
		$this->log->debug("查看管理员分页");
		$params = array_map("trim", [
			'page' => request('page', 1),
			'limit' => request('limit', 20),
		]);

		$domain = AdminInfo::getDomain();

		$skip = ($params['page'] - 1) * $params['limit'];
		$select = "s_admin_info.id,
                    s_admin_info.nickname,
                    s_admin_info.img_id,
                    concat('{$domain}/', c.dirType, '/', c.name)  as img_url,
                    s_admin_info.status,
                    s_admin_info.uid,
                    b.name,
                    b.email,
                    b.created_at,
                    b.updated_at,
                    d.role_id,
                    e.name as role_name";
		$table = AdminInfo::selectRaw($select);

		$count = $table->count();
		$users = $table
			->leftJoin('users as b', 'b.id', '=', 's_admin_info.uid')
			->leftJoin('bw_file as c', 'c.id', '=', 's_admin_info.img_id')
			->leftJoin('role_user as d', 'd.user_id', '=', 's_admin_info.uid')
			->leftJoin('roles as e', 'e.id', '=', 'd.role_id')
			->skip($skip)
			->limit($params['limit'])
			->orderBy('s_admin_info.id', 'desc')
			->get();

		$totalPage = ceil($count / $params['limit']);

		$data = [
			'data' => $users->toArray(), // 当前页记录
			'totalNum' => $count, // 记录条数
			'currentPage' => $params['page'], // 当前页
			'totalPage' => $totalPage, // 总页数
			// 'params' => $params,
		];

		return response()->json(['code' => $this->successStatus,
			'success' => $data], $this->successStatus);

	}

}