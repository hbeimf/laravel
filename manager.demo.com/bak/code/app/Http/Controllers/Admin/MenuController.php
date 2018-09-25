<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Model\AdminInfo;
use App\Http\Model\Menu;
use App\Http\Model\Menusub;
use App\Http\Model\Permissions;
use App\Http\Model\Roles;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends CommonController {

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getMenu",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取导航 分页 action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="getMenu",
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
	 *                      property="parent_id",
	 *                      type="int",
	 *                      description="parent_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="permission_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="api_link"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="is_enable"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_show",
	 *                      type="int",
	 *                      description="is_show"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="order_by",
	 *                      type="int",
	 *                      description="导航排序"
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
	public function getMenu() {
		$this->log->debug("查看导航分页");

		$params = array_map("trim", [
			'page' => request('page', 1),
			'limit' => request('limit', 20),
		]);

		$skip = ($params['page'] - 1) * $params['limit'];
		$select = 'id, parent_id, permission_id, name, api_link, url, icon, is_enable, description as `desc`, created_at, updated_at, order_by, is_show';
		$table = Menu::selectRaw($select);

		$table->where('parent_id', '=', "0");

		$count = $table->count();
		$rows = $table
			->skip($skip)
			->limit($params['limit'])
			->orderBy('order_by', 'asc')
			->get();

		$totalPage = ceil($count / $params['limit']);

		$data = [
			'data' => $this->getChildren($rows->toArray()), // 当前页记录
			'totalNum' => $count, // 记录条数
			'currentPage' => $params['page'], // 当前页
			'totalPage' => $totalPage, // 总页数
			// 'params' => $params,
		];

		return response()->json(['code' => $this->successStatus,
			'success' => $data], $this->successStatus);

	}

	private function getChildren($rows) {
		$t = new Menu();

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$children = $t->getRowByParentId($row['id']);
				if (!empty($children)) {
					for ($i = 0; $i < count($children); $i++) {
						$children[$i]['children'] = $t->getRowByParentId($children[$i]['id']);
					}
				}
				$row['children'] = $children;
				// $row['children'] = $t->getRowByParentId($row['id']);

				$reply[] = $row;
			}
		}

		return $reply;
	}

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getMenuInner",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取内部导航 分页 action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="getMenu",
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
	 *                      property="menu_id",
	 *                      type="int",
	 *                      description="menu_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="permission_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="controller",
	 *                      type="string",
	 *                      description="controller"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="api_link"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="is_enable"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
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
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="menu_name",
	 *                      type="string",
	 *                      description="menu_name"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_name",
	 *                      type="string",
	 *                      description="permission_name"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function getMenuInner() {
		$this->log->debug("查看内部导航分页");

		$params = array_map("trim", [
			'page' => request('page', 1),
			'limit' => request('limit', 20),
		]);

		$skip = ($params['page'] - 1) * $params['limit'];
		$select = 's_menu_sub.id,
			s_menu_sub.menu_id,
			s_menu_sub.permission_id,
			s_menu_sub.controller,
			s_menu_sub.api_link,
			s_menu_sub.is_enable,
			s_menu_sub.description as `desc`,
			s_menu_sub.created_at,
			s_menu_sub.updated_at,
			b.name as menu_name,
			c.name as permission_name';

		$table = Menusub::selectRaw($select);

		$count = $table->count();
		$users = $table
			->leftJoin('s_menu as b', 'b.id', '=', 's_menu_sub.menu_id')
			->leftJoin('permissions as c', 'c.id', '=', 's_menu_sub.permission_id')
			->skip($skip)
			->limit($params['limit'])
			->orderBy('id', 'desc')
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

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getMenuAll",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取导航 action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="getMenu",
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
	 *                      property="parent_id",
	 *                      type="int",
	 *                      description="parent_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="permission_id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="api_link"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="is_enable"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_show",
	 *                      type="int",
	 *                      description="is_show"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
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
	public function getMenuAll() {
		$this->log->debug("查看导航");

		$select = 'id, parent_id, permission_id, name, api_link, url, icon, is_enable, description as `desc`, created_at, updated_at, is_show';
		$table = Menu::selectRaw($select);
		$table->where('parent_id', '=', "0");
		$rows = $table
			->orderBy('id', 'desc')
			->get();

		$reply = $this->getChildren($rows->toArray());

		return response()->json(['code' => $this->successStatus,
			'success' => $reply], $this->successStatus);
	}

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getUserInfo",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="getUserInfo",
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
	 *                      property="parent_id",
	 *                      type="int",
	 *                      description="上级id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="关联权限id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="链接"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="状态[1:启用, 0:禁用]"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
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
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="inner",
	 *                      type="string",
	 *                      description="内部功能列表"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function getUserInfo() {
		$this->log->debug("获取用户信息");
		$user = Auth::user();

		$tableAdmin = new AdminInfo();
		$adminInfo = $tableAdmin->getAdminInfo($user->id);

		$tableRole = new Roles();
		$roles = $tableRole->getRoleByUid($user->id);

		// 用户拥有权限 ids
		$permissionIds = $this->getPermissionIds($roles['id']);

		$reply = [
			'uid' => $user->id,
			'nickname' => $adminInfo['nickname'],
			'image' => $adminInfo['image'],
			'role_id' => $roles['id'],
			'role_name' => $roles['name'],
			'menu' => $this->getMenuByUid($user->id, $permissionIds),
			// 'permission_ids' => $permissionIds,
		];
		return response()->json(['code' => $this->successStatus,
			'success' => $reply], $this->successStatus);

	}

	private function getPermissionIds($roleId) {
		$tablePermission = new Permissions();
		$permissionIds = $tablePermission->getPermissionByRole($roleId);

		$ids = [];
		if (!empty($permissionIds)) {
			foreach ($permissionIds as $p) {
				$ids[] = $p['id'];
			}
		}
		return $ids;
	}

	private function getMenuByUid($uid, $permissionIds) {
		$p = new Permissions();

		$t = new Menu();
		$mm = $t->getMenu();

		$t1 = new Menusub();
		$menuSub = $t1->getMenu();

		// $theMenu = [];
		// foreach ($mm as $m) {
		// 	if ($m['parent_id'] == 0) {
		// 		$node = $this->getChildMenu($m, $mm, $menuSub, $permissionIds);
		// 		if (!empty($node['children'])) {
		// 			$theMenu[] = $node;
		// 		}
		// 	}
		// }
		// return $theMenu;

		$theMenu = [];
		foreach ($mm as $m) {
			if (in_array($m['permission_id'], $permissionIds)) {
				$m['inner'] = $this->getMenuSub($m, $menuSub, $permissionIds);
				$theMenu[] = $m;
			}
		}

		return $theMenu;
	}

	private function getChildMenu($menu, $menus, $menuSub, $permissionIds) {
		$menu['children'] = [];
		foreach ($menus as $m1) {
			// if (in_array($m1['permission_id'], $permissionIds) && $m1['parent_id'] == $menu['id']) {
			if ($m1['parent_id'] == $menu['id']) {

				$m1['inner'] = $this->getMenuSub($m1, $menuSub, $permissionIds);
				$menu['children'][] = $m1;
			}
		}

		return $menu;
	}

	private function getMenuSub($menu, $menuSub, $permissionIds) {
		$inner = [];
		if (!empty($menuSub)) {
			foreach ($menuSub as $m) {
				// if (in_array($m['permission_id'], $permissionIds) && $menu['id'] == $m['menu_id']) {
				if ($menu['id'] == $m['menu_id']) {

					$inner[] = $m;
				}
			}
		}
		return $inner;
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/createMenu",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建导航action",
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
	 *          name="name",
	 *          type="string",
	 *          description="导航名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="parent_id",
	 *          type="integer",
	 *          description="上级导航节点id",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="permission_id",
	 *          type="integer",
	 *          description="权限id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="api_link",
	 *          type="string",
	 *          description="访问链接",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="url",
	 *          type="string",
	 *          description="url",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="icon",
	 *          type="string",
	 *          description="icon",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="状态[1:启用, 0:禁用]",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_show",
	 *          type="integer",
	 *          description="状态[1:显示, 0:不显示]",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="order_by",
	 *          type="integer",
	 *          description="导航排序",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="desc",
	 *          type="string",
	 *          description="描述",
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
	 *                      property="parent_id",
	 *                      type="int",
	 *                      description="上级id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="关联权限id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="链接"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="状态[1:启用, 0:禁用]"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_show",
	 *                      type="int",
	 *                      description="状态[1:显示, 0:不显示]"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="order_by",
	 *                      type="int",
	 *                      description="导航排序"
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
	public function createMenu() {
		$this->log->debug("新增导航");
		$params = array_map("trim", [
			'parent_id' => request('parent_id', 0),
			'permission_id' => request('permission_id', 0),
			'name' => request('name', ''),
			'api_link' => request('api_link', ''),
			'url' => request('url', ''),
			'icon' => request('icon', ''),
			'is_enable' => request('is_enable', 0),
			'is_show' => request('is_enable', 0),
			'description' => request('desc', ''),
			'order_by' => request('order_by', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '导航名称不能为空！'], $this->successStatus);
		} elseif ($params['permission_id'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限id不能为空！'], $this->successStatus);
		}

		$t = new Menu();
		$r = $t->createMenu($params['name'], $params['permission_id'],
			$params['api_link'], $params['url'], $params['icon'], $params['parent_id'], $params['is_enable'], $params['description'], $params['order_by'], $params['is_show']);

		return response()->json(['code' => $this->successStatus,
			'success' => $r->toArray()], $this->successStatus);
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/createMenuInner",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建内部导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="createMenuInner",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="menu_id",
	 *          type="integer",
	 *          description="关联导航id",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="permission_id",
	 *          type="integer",
	 *          description="权限id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="controller",
	 *          type="string",
	 *          description="控制器",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="api_link",
	 *          type="string",
	 *          description="访问链接",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="desc",
	 *          type="string",
	 *          description="描述",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="状态[1:启用, 0:禁用]",
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
	 *                      property="menu_id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="关联权限id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="controller",
	 *                      type="string",
	 *                      description="控制器名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="链接"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
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
	public function createMenuInner() {
		$this->log->debug("新增导航内子功能");
		$params = array_map("trim", [
			'menu_id' => request('menu_id', 0),
			'permission_id' => request('permission_id', 0),
			'controller' => request('controller', ''),
			'api_link' => request('api_link', ''),
			'description' => request('desc', ''),
			'is_enable' => request('is_enable', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['menu_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '所属菜单不能为空！'], $this->successStatus);
		} elseif ($params['permission_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限id不能为空！'], $this->successStatus);
		} elseif ($params['controller'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '控制器不能为空！'], $this->successStatus);
		} elseif ($params['api_link'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '接口地址不能为空！'], $this->successStatus);
		}

		$t = new Menusub();
		$r = $t->createMenu($params['menu_id'], $params['permission_id'], $params['controller'],
			$params['api_link'], $params['description'], $params['is_enable']);

		return response()->json(['code' => $this->successStatus,
			'success' => $t->getMenuById($r->id)], $this->successStatus);
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/updateMenu",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="updateMenu",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="integer",
	 *          description="导航id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="导航名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="parent_id",
	 *          type="integer",
	 *          description="上级导航节点id",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="permission_id",
	 *          type="integer",
	 *          description="权限id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="api_link",
	 *          type="string",
	 *          description="访问链接",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="url",
	 *          type="string",
	 *          description="url",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="icon",
	 *          type="string",
	 *          description="icon",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="状态[1:启用, 0:禁用]",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_show",
	 *          type="integer",
	 *          description="状态[1:显示, 0:不显示]",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="order_by",
	 *          type="integer",
	 *          description="导航排序",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="desc",
	 *          type="string",
	 *          description="描述",
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
	 *                      property="parent_id",
	 *                      type="int",
	 *                      description="上级id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="关联权限id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="链接"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_enable",
	 *                      type="int",
	 *                      description="状态[1:启用, 0:禁用]"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="is_show",
	 *                      type="int",
	 *                      description="状态[1:显示, 0:不显示]"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="created_at",
	 *                      type="timestamp",
	 *                      description="创建时间"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="order_by",
	 *                      type="int",
	 *                      description="导航排序"
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
	public function updateMenu() {
		$this->log->debug("更新导航");
		$params = array_map("trim", [
			'id' => request('id', 0),
			'parent_id' => request('parent_id', 0),
			'permission_id' => request('permission_id', 0),
			'name' => request('name', ''),
			'api_link' => request('api_link', ''),
			'url' => request('url', ''),
			'icon' => request('icon', ''),
			'is_enable' => request('is_enable', 0),
			'order_by' => request('order_by', 0),
			'description' => request('desc', ''),
			'is_show' => request('is_show', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		} elseif ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '导航名称不能为空！'], $this->successStatus);
		} elseif ($params['permission_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限id不能为空！'], $this->successStatus);
		}

		$t = new Menu();
		$r = $t->updateMenu($params['id'], $params['name'], $params['permission_id'],
			$params['api_link'], $params['url'], $params['icon'], $params['parent_id'], $params['is_enable'], $params['description'], $params['order_by'], $params['is_show']);

		return response()->json(['code' => $this->successStatus,
			'success' => $t->getRowById($params['id'])], $this->successStatus);
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/updateMenuInner",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="更新内部导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="updateMenuInner",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="integer",
	 *          description="导航id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="menu_id",
	 *          type="integer",
	 *          description="关联导航id",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="permission_id",
	 *          type="integer",
	 *          description="权限id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="controller",
	 *          type="string",
	 *          description="控制器",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="api_link",
	 *          type="string",
	 *          description="访问链接",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="desc",
	 *          type="string",
	 *          description="描述",
	 *          required=false,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="状态[1:启用, 0:禁用]",
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
	 *                      property="menu_id",
	 *                      type="int",
	 *                      description="导航id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_id",
	 *                      type="int",
	 *                      description="关联权限id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="controller",
	 *                      type="string",
	 *                      description="控制器名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="api_link",
	 *                      type="string",
	 *                      description="链接"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
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
	public function updateMenuInner() {
		$this->log->debug("更新导航内子功能");
		$params = array_map("trim", [
			'id' => request('id', 0),
			'menu_id' => request('menu_id', 0),
			'permission_id' => request('permission_id', 0),
			'controller' => request('controller', ''),
			'api_link' => request('api_link', ''),
			'description' => request('desc', ''),
			'is_enable' => request('is_enable', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['menu_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'menu_id不能为空！'], $this->successStatus);
		} elseif ($params['permission_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限id不能为空！'], $this->successStatus);
		} elseif ($params['controller'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '控制器不能为空！'], $this->successStatus);
		} elseif ($params['api_link'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '接口地址不能为空！'], $this->successStatus);
		}

		$t = new Menusub();
		$r = $t->updateMenu($params['id'], $params['menu_id'], $params['permission_id'],
			$params['controller'], $params['api_link'], $params['description'], $params['is_enable']);

		return response()->json(['code' => $this->successStatus,
			'success' => $t->getMenuById($params['id'])], $this->successStatus);
	}

	/**
	 * del导航action
	 *
	 * @SWG\Post(
	 *      path="/api/delMenu",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="updateMenu",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="integer",
	 *          description="导航id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="删除成功",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="success",
	 *	         type="string"
	 *	    )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function delMenu() {
		$this->log->debug("删除导航");

		$params = array_map("trim", [
			'id' => request('id', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		}

		$t = new Menu();
		$r = $t->del($params['id']);

		return response()->json(['code' => $this->successStatus,
			'success' => $r], $this->successStatus);

	}

	/**
	 * del导航action
	 *
	 * @SWG\Post(
	 *      path="/api/delMenuInner",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="删除内部导航action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="delMenuInner",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="integer",
	 *          description="导航id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="删除成功",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="success",
	 *	         type="string"
	 *	    )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function delMenuInner() {
		$this->log->debug("删除内部页面导航");

		$params = array_map("trim", [
			'id' => request('id', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		}

		$t = new Menusub();
		$r = $t->del($params['id']);

		return response()->json(['code' => $this->successStatus,
			'success' => $r], $this->successStatus);

	}

}