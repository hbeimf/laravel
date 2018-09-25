<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Model\Menu;
use App\Http\Model\Permissions;
use App\Http\Model\Roles;
use App\User;
use Illuminate\Http\Request;

class RoleController extends CommonController {

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/createRole",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建角色action",
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
	 *          description="permission名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="slug",
	 *          type="string",
	 *          description="资源，如 user.create",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="permission_ids",
	 *          type="string",
	 *          description="资源id列表,类型为列表",
	 *          required=true,
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
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="slug",
	 *                      type="string",
	 *                      description="资源"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="desc",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="level",
	 *                      type="integer",
	 *                      description="level"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function create() {
		$this->log->debug("创建角色");

		$params = array_map("trim", [
			'name' => request('name', ''),
			'slug' => request('slug', ''),
			'desc' => request('desc', ''),
			'level' => request('level', 0),
		]);

		if ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色名称不能为空！'], $this->successStatus);
		} elseif ($params['slug'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '资源唯一标识不能为空！'], $this->successStatus);
		}

		try {
			$table_role = new Roles();

			if ($table_role->existsRole($params['slug'])) {
				return response()->json(['code' => $this->errorStatus,
					'error' => '唯一标志重复！'], $this->successStatus);
			} else {
				$role = $table_role->createRole($params['name'], $params['slug'],
					$params['desc'], $params['level']);

				// 关联role & permissions
				$permissionIds = request('permission_ids', []);
				if (!empty($permissionIds)) {
					$t = new Permissions();
					$t->detachAllPermissions($role->id);
					foreach ($permissionIds as $permissionId) {
						$t->attachRoleAndPermission($permissionId, $role->id);
					}
				}

				$role->permission_ids = $this->getRolePermissionIds($role->id);

				return response()->json(['code' => $this->successStatus,
					'success' => $role], $this->successStatus);
			}
		} catch (Exception $e) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $e->getMessage()], $this->successStatus);
		}
	}

	private function getRolePermissionIds($roleId) {
		$t = new Permissions();
		return $t->getPermissionByRole($roleId);
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/delRole",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="删除角色action",
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
	 *          description="权限资源id",
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
	public function del() {
		$this->log->debug("删除角色!");

		$params = array_map("trim", [
			'id' => request('id', 0),
		]);

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		}

		$t = new Roles();
		if (!$t->isSuperadmin($params['id'])) {
			$t->del($params['id']);

			return response()->json(['code' => $this->successStatus,
				'success' => 1], $this->successStatus);
		} else {
			return response()->json(['code' => $this->errorStatus,
				'error' => '不能删除超管理员！'], $this->successStatus);
		}
	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/updateRole",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="更新角色action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="",
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
	 *          description="role id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="slug",
	 *          type="string",
	 *          description="角色",
	 *          required=true,
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
	 *          name="permission_ids",
	 *          type="string",
	 *          description="资源id列表,类型为列表",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了"
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="数组",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 *                      property="id",
	 *                      type="int",
	 *                      description="id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="slug",
	 *                      type="string",
	 *                      description="资源"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="desc",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="level",
	 *                      type="integer",
	 *                      description="level"
	 *                  )
	 *          )
	 *      )
	 *   )
	 * )
	 */
	public function update() {
		$this->log->debug("修改角色");

		$params = array_map("trim", [
			'id' => request('id', 0),
			'name' => request('name', ''),
			'slug' => request('slug', ''),
			'desc' => request('desc', ''),
			'level' => request('level', 0),
		]);

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		} elseif ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色名称不能为空！'], $this->successStatus);
		} elseif ($params['slug'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色唯一标识不能为空！'], $this->successStatus);
		}

		$role = new Roles();
		if ($role->isSuperadmin($params['id'])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '不能编辑超管理员！'], $this->successStatus);
		}

		if (!$role->existsRoleById($params['id'], $params['slug'])) {
			if (!$role->isSuperadmin($params['id'])) {

				$role->updateRole($params['id'], $params['name'], $params['slug'],
					$params['desc'], $params['level']);

				// 关联role & permissions
				$permissionIds = request('permission_ids', []);
				if (!empty($permissionIds)) {
					$t = new Permissions();
					$t->detachAllPermissions($params['id']);
					foreach ($permissionIds as $permissionId) {
						$t->attachRoleAndPermission($permissionId, $params['id']);
					}
				}

				$reply = $role->getRoleById($params['id']);
				$reply->permission_ids = $this->getRolePermissionIds($params['id']);

				return response()->json(['code' => $this->successStatus,
					'success' => $reply], $this->successStatus);
			} else {
				return response()->json(['code' => $this->errorStatus,
					'error' => '不能修改超级管理员！'], $this->successStatus);
			}
		} else {
			return response()->json(['code' => $this->errorStatus,
				'error' => '资源唯一标识不能重复！'], $this->successStatus);
		}
	}

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getRoleAll",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取角色 action",
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
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="slug",
	 *                      type="string",
	 *                      description="角色"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="level",
	 *                      type="integer",
	 *                      description="暂时无用"
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
	public function getAll() {
		$this->log->debug("查看角色");

		$t = new Roles();
		$roles = $t->allRoles();
		return response()->json(['code' => $this->successStatus,
			'success' => $roles], $this->successStatus);
	}

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getRole",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取角色 分页action",
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
	 *                      property="name",
	 *                      type="string",
	 *                      description="名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="slug",
	 *                      type="string",
	 *                      description="角色"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="permission_ids",
	 *                      type="string",
	 *                      description="权限资源列表,字段: id, name, slug, description"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="level",
	 *                      type="integer",
	 *                      description="暂时无用"
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
	public function get() {
		$this->log->debug("查看角色分页");

		$params = array_map("trim", [
			'page' => request('page', 1),
			'limit' => request('limit', 20),
		]);

		$skip = ($params['page'] - 1) * $params['limit'];
		$select = 'id, name, slug, description, created_at, updated_at';

		$table = Roles::selectRaw($select);

		$count = $table->count();
		$roles = $table
			->skip($skip)
			->limit($params['limit'])
			->orderBy('id', 'desc')
			->get();

		$totalPage = ceil($count / $params['limit']);

		$data = [
			// 'data' => $roles->toArray(), // 当前页记录
			'data' => $this->getRole($roles->toArray()), // 当前页记录
			'totalNum' => $count, // 记录条数
			'currentPage' => $params['page'], // 当前页
			'totalPage' => $totalPage, // 总页数
			// 'params' => $params,
		];

		return response()->json(['code' => $this->successStatus,
			'success' => $data], $this->successStatus);

	}

	private function getRole($roles) {
		$t = new Permissions();
		$reply = [];
		foreach ($roles as $role) {
			# code...
			$role['permission_ids'] = $t->getPermissionByRole($role['id']);
			$reply[] = $role;
		}
		return $reply;
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/attachRoleAndUser",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="关联角色和用户action",
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
	 *          name="user_id",
	 *          type="integer",
	 *          description="用户id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="关联失败",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="关联成功",
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
	public function attachRoleAndUser() {
		$this->log->debug("关联角色与账号！");

		$params = array_map("trim", [
			'user_id' => request('user_id', 0),
			'role_id' => request('role_id', 0),
		]);

		if ($params['user_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '账户不能为空！'], $this->successStatus);
		} elseif ($params['role_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色不能为空！'], $this->successStatus);
		}

		$t = new Roles();
		$t->attachRole($params['user_id'], $params['role_id']);

		return response()->json(['code' => $this->successStatus,
			'success' => 1], $this->successStatus);
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/detachRoleAndUser",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="取消关联角色和用户action",
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
	 *          name="user_id",
	 *          type="integer",
	 *          description="用户id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="关联失败",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="关联成功",
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
	public function detachRoleAndUser() {
		$this->log->debug("关联角色与账号！");

		$params = array_map("trim", [
			'user_id' => request('user_id', 0),
			'role_id' => request('role_id', 0),
		]);

		if ($params['user_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '账户不能为空！'], $this->successStatus);
		} elseif ($params['role_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色不能为空！'], $this->successStatus);
		}

		$t = new Roles();
		$t->detachRole($params['user_id'], $params['role_id']);

		return response()->json(['code' => $this->successStatus,
			'success' => 1], $this->successStatus);
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/attachRoleAndPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="关联角色和用户action",
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
	 *          name="permission_id",
	 *          type="integer",
	 *          description="permission id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="关联失败",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="关联成功",
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
	public function attachRoleAndPermission() {
		$this->log->debug("关联角色与资源！");

		$params = array_map("trim", [
			'permission_id' => request('permission_id', 0),
			'role_id' => request('role_id', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['permission_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'permission_id'], $this->successStatus);
		} elseif ($params['role_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色不能为空！'], $this->successStatus);
		}

		$t = new Permissions();
		$t->attachRoleAndPermission($params['permission_id'], $params['role_id']);

		return response()->json(['code' => $this->successStatus,
			'success' => 1], $this->successStatus);
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/detachRoleAndPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="取消关联角色和用户action",
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
	 *          name="permission_id",
	 *          type="integer",
	 *          description="permission id",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="role_id",
	 *          type="integer",
	 *          description="角色id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="关联失败",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="关联成功",
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
	public function detachRoleAndPermission() {
		$this->log->debug("取消关联角色与资源！");

		$params = array_map("trim", [
			'permission_id' => request('permission_id', 0),
			'role_id' => request('role_id', 0),
		]);

		$this->log->debug(json_encode($params));

		if ($params['permission_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'permission_id'], $this->successStatus);
		} elseif ($params['role_id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '角色不能为空！'], $this->successStatus);
		}

		$t = new Permissions();
		$t->detachRoleAndPermission($params['permission_id'], $params['role_id']);

		return response()->json(['code' => $this->successStatus,
			'success' => 1], $this->successStatus);
	}

}