<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Model\Menu;
use App\Http\Model\Permissions;
use App\Http\Model\Roles;
use App\User;
use Illuminate\Http\Request;

// https://github.com/jeremykenedy/laravel-roles
class PermissionController extends CommonController {

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/createPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="创建权限资源action",
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
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="model",
	 *                      type="string",
	 *                      description="model 暂时无用"
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
	public function create() {
		$this->log->debug("创建资源");

		$params = array_map("trim", [
			'name' => request('name', ''),
			'slug' => request('slug', ''),
			'desc' => request('desc', ''),
		]);

		$this->log->debug(json_encode($params));

		if ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限名称不能为空！'], $this->successStatus);
		} elseif ($params['slug'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '资源唯一标识不能为空！'], $this->successStatus);
		}

		$t = new Permissions();

		if ($t->existsPermission($params['slug'])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => $params['slug'] . '已添加过，请不要重复添加！'], $this->successStatus);
		}

		$permission = $t->createPermission($params['name'], $params['slug'],
			$params['desc']);

		$role = new Roles();
		$this->log->debug("自动关联超级管理员" . $permission->id);
		$superadmin = $role->getSuperadmin();
		$t->attachRoleAndPermission($permission->id, $superadmin->id);
		// $t->attachRoleAndPermission($permissionId, $params['id']);

		return response()->json(['code' => $this->successStatus,
			'success' => $permission], $this->successStatus);

	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/delPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="删除Permission action",
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
		$this->log->debug("删除资源");

		$params = array_map("trim", [
			'id' => request('id', 0),
		]);

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		}

		$t = new Permissions();

		$t->del($params['id']);

		return response()->json(['code' => $this->successStatus,
			'success' => 1], $this->successStatus);

	}

	/**
	 * 新建导航action
	 *
	 * @SWG\Post(
	 *      path="/api/updatePermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="更新权限资源action",
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
	 *          description="permission id",
	 *          required=true,
	 *      ),
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
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="model",
	 *                      type="string",
	 *                      description="model 暂时无用"
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
	public function update() {
		$this->log->debug("更新资源");

		$params = array_map("trim", [
			'id' => request('id', 0),
			'name' => request('name', ''),
			'slug' => request('slug', ''),
			'desc' => request('desc', ''),
		]);

		if ($params['id'] == 0) {
			return response()->json(['code' => $this->errorStatus,
				'error' => 'id不能为空！'], $this->successStatus);
		} elseif ($params['name'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '权限名称不能为空！'], $this->successStatus);
		} elseif ($params['slug'] == '') {
			return response()->json(['code' => $this->errorStatus,
				'error' => '资源唯一标识不能为空！'], $this->successStatus);
		}

		$t = new Permissions();

		if ($t->existsPermissionById($params['id'], $params['slug'])) {
			return response()->json(['code' => $this->errorStatus,
				'error' => '资源唯一标识不能重复添加！'], $this->successStatus);
		}

		$permission = $t->updatePermission($params['id'], $params['name'], $params['slug'],
			$params['desc']);

		return response()->json(['code' => $this->successStatus,
			'success' => $t->getRowById($params['id'])], $this->successStatus);

	}

	/**
	 * 获取导航action
	 *
	 * @SWG\Post(
	 *      path="/api/getPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取权限资源  分页action",
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
	 *                      description="资源"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="model",
	 *                      type="string",
	 *                      description="model 暂时无用"
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
		$this->log->debug("查看资源");

		$params = array_map("trim", [
			'page' => request('page', 1),
			'limit' => request('limit', 20),
		]);

		$skip = ($params['page'] - 1) * $params['limit'];
		$select = 'id, name, slug, description as `desc`, created_at, updated_at';

		$table = Permissions::selectRaw($select);

		$count = $table->count();
		$users = $table
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
	 *      path="/api/getPermissionAll",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="获取权限资源action",
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
	 *                      description="资源"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="description",
	 *                      type="string",
	 *                      description="备注信息"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="model",
	 *                      type="string",
	 *                      description="model 暂时无用"
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
		$this->log->debug("查看所有资源");

		$t = new Permissions();
		$all = $t->allPermissions();
		return response()->json(['code' => $this->successStatus,
			'success' => $all], $this->successStatus);
	}

	/**
	 * del action
	 *
	 * @SWG\Post(
	 *      path="/api/hasPermission",
	 *      tags={"admin-后台账号权限相关-maomao"},
	 *      summary="用户是否有权限访问某个资源action",
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
	 *          name="permission_id",
	 *          type="integer",
	 *          description="资源id",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="无权限",
	 *	@SWG\Schema(
	 *	    @SWG\Property(
	 *	         property="error",
	 *	         type="string"
	 *	    )
	 *	)
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="有权限",
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
	public function has() {
		$this->log->debug("检查是否有权限 ");

		$params = array_map("trim", [
			'user_id' => request('user_id'),
			'permission_id' => request('permission_id'),
		]);

		$t = new Permissions();
		if ($t->hasPermission($params['user_id'], $params['permission_id'])) {
			return response()->json(['code' => $this->successStatus,
				'success' => '1'], $this->successStatus);
		} else {
			return response()->json(['code' => $this->errorStatus,
				'error' => '没有权限！'], $this->successStatus);
		}
	}

}