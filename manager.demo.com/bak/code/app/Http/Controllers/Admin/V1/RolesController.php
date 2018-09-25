<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Http\Model\Permissions;
use App\Http\Model\Roles;
use App\Repository\RolesRepository;
use App\Validator\RolesValidator;
use App\Transformer\RolesTransformer;
use Illuminate\Http\Request;

class RolesController extends BaseController {

	protected $validator;
	protected $repo;

	public function __construct(RolesRepository $_repo, RolesValidator $_validator) {
		parent::__construct();
		$this->repo = $_repo;
		$this->validator = $_validator;

	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/roles",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="取款资源列表 分页action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="permissions",
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
	 *          response=200,
	 *          description="取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
	 *	         type="array",
	 *                   @SWG\Items(
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
	 *     		)
	 *     	),
	 *	@SWG\Property(
	 *		property="meta",
	 *	             @SWG\Property(
	 *                               property="pagination",
	 *                 	       @SWG\Items(
	 *                 	             @SWG\Property(
	 *                 	                  property="total",
	 *                 	                  type="int",
	 *                 	                  description="总条数"
	 *                 	              ),
	 *                 	              @SWG\Property(
	 *                 	                  property="count",
	 *                 	                  type="int",
	 *                 	                  description="当前页条数"
	 *                 	              ),
	 *                 	              @SWG\Property(
	 *                 	                  property="per_page",
	 *                 	                  type="int",
	 *                 	                  description="每页条数"
	 *                 	              ),
	 *                 	              @SWG\Property(
	 *                 	                  property="current_page",
	 *                 	                  type="int",
	 *                 	                  description="当前第几页"
	 *                 	              ),
	 *                 	              @SWG\Property(
	 *                 	                  property="total_pages",
	 *                 	                  type="int",
	 *                 	                  description="总页数"
	 *                 	              ),
	 *                 	              @SWG\Property(
	 *				                  property="links",
	 *                 	       		  @SWG\Items(
	 *                 	             			@SWG\Property(
	 *                 	                                              property="previous",
	 *                 	                                              type="string",
	 *                 	                                              description="上一页链接 ,如果是首页，则无此属性"
	 *                 	                                          ),
	 *                 	             			@SWG\Property(
	 *                 	                                              property="next",
	 *                 	                                              type="string",
	 *                 	                                              description="下一页链接 ，如果是最后一页，则无此属性"
	 *                 	                                          )
	 *
	 *				)
	 *                 	              )
	 *     	           	 )
	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function list() {
		$limit = request('limit', 20);
		return $this->response()->paginator($this->repo->paginate($limit), new RolesTransformer());
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/roles/{id}",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="根据 { id } 获取单条资源action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="permissions",
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
	 *          response=200,
	 *          description="单条取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
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
	 *
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function get($id) {
		return $this->response()->item($this->repo->find($id), new RolesTransformer());
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Post(
	 *      path="/v1/roles",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="新增权限资源action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="withdrawMoney",
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
	 *          response=200,
	 *          description="单条取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
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
	 *
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function create(Request $request) {
		$this->validator->with($request->all())->passesOrFail('create');
		$model = $this->repo->create($request->all());
		
		// 关联role & permissions
		$permissionIds = request('permission_ids', []);
		if (!empty($permissionIds)) {
		    $t = new Permissions();
		    $t->detachAllPermissions($model->id);
		    foreach ($permissionIds as $permissionId) {
		        $t->attachRoleAndPermission($permissionId, $model->id);
		    }
		}

		$response = [
			'message' => '数据创建成功.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Put(
	 *      path="/v1/roles/{id}",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="修改权限资源action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="permissions",
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
	 *          name="description",
	 *          type="string",
	 *          description="描述",
	 *          required=false,
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="单条取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
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
	 *
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function update(Request $request, $id) {

		$this->validator->with($request->all())->setId($id)->passesOrFail('update');
		$params = $request->all();
		
		$role = new Roles();
		if ($role->isSuperadmin($id)) {
		    $response = [
		        'message' => '不能编辑超级管理员.',
		    ];
		    return $this->response()->array($response);
		}

		$model = $this->repo->update($params, $id);
		
		// 关联role & permissions
		$permissionIds = request('permission_ids', []);
		if (!empty($permissionIds)) {
		    $t = new Permissions();
		    $t->detachAllPermissions($id);
		    foreach ($permissionIds as $permissionId) {
		        $t->attachRoleAndPermission($permissionId, $id);
		    }
		}

		$response = [
			'message' => '更新成功.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);

	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Delete(
	 *      path="/v1/roles/{id}",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="删除取款模式action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="permissions",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=200,
	 *          description="删除资源",
	 *          @SWG\Schema(
	 *                              @SWG\Property(
	 *                                   property="message",
	 *                                   type="string",
	 *                                   description="提示消息"
	 *                               )
	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function destroy($id) {
		$row = $this->repo->find($id);
		
		$t = new Roles();
		if ($t->isSuperadmin($id)) {
		    $response = [
		        'message' => '不能删除超管理员！',
		    ];
		    return $this->response()->array($response);
		}

		$this->repo->delete($id);
		$response = [
			'message' => '删除成功.',
		];
		return $this->response()->array($response);
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/rolesAll",
	 *      tags={"admin-角色管理{重构}-maomao"},
	 *      summary="取款所有资源列表action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="permissionsAll",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=200,
	 *          description="取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
	 *	         type="array",
	 *                   @SWG\Items(
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
	 *     		)
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function all() {
		return $this->response()->collection($this->repo->all(), new RolesTransformer());
	}

}