<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\UserGroupRepository;
use App\Transformer\UserGroupTransformer;
use App\Validator\UserGroupValidator;
use Illuminate\Http\Request;

class UserGroupController extends BaseController
{
    protected $validator;

    protected $repo;


    public function __construct(UserGroupRepository $_repo, UserGroupValidator $_validator) {
        parent::__construct();
        $this->repo = $_repo;
        $this->validator = $_validator;

    }
    /**
     * @SWG\Definition(
     *     definition="UserGroup",
     *                                             @SWG\Property(
     *                              property="uid",
     *                              type="integer",
     *                              description="分组ID"
     *                             ),
     *                            @SWG\Property(
     *                              property="name",
     *                              type="string",
     *                              description="分组名称"
     *                             ),
     *                            @SWG\Property(
     *                              property="begin_time",
     *                              type="string",
     *                              description="用户加入期间，开始时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="end_time",
     *                              type="string",
     *                              description="用户加入期间，结束时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="theme_limit",
     *                              type="integer",
     *                              description="主题限制"
     *                             ),
     *                            @SWG\Property(
     *                              property="deposit_time",
     *                              type="integer",
     *                              description="存款次数"
     *                             ),
     *                            @SWG\Property(
     *                              property="total_deposit_money",
     *                              type="integer",
     *                              description="存款总额，历次存款额度"
     *                             ),
     *                            @SWG\Property(
     *                              property="max_deposit_money",
     *                              type="integer",
     *                              description="最大存款金额"
     *                             ),
     *                            @SWG\Property(
     *                              property="withdraw_time",
     *                              type="integer",
     *                              description="提款次数"
     *                             ),
     *                            @SWG\Property(
     *                              property="withdraw_money",
     *                              type="integer",
     *                              description="提款总额"
     *                             ),
     *                            @SWG\Property(
     *                              property="note",
     *                              type="string",
     *                              description="添加备注"
     *                             ),
     *                            @SWG\Property(
     *                              property="is_auto_group",
     *                              type="integer",
     *                              description="用户自动分组，0:关闭， 1：启用"
     *                             ),
     *                            @SWG\Property(
     *                              property="default",
     *                              type="integer",
     *                              description="默认组状态 :1.默认组  0.不是默认组 默认组不能删除"
     *                             ),
     *                            @SWG\Property(
     *                              property="created_at",
     *                              type="string",
     *                              description="创建时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="updated_at",
     *                              type="string",
     *                              description="修改时间"
     *                              ),
     *
     * )
     */


    /**
     *
     *
     * @SWG\Post(
     *      path="/api/userGroup",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="添加用户分组",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *
     *       @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="分组名称",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="begin_time",
     *          type="string",
     *          description="用户加入期间，开始时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="ent_time",
     *          type="string",
     *          description="用户加入期间，结束时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="theme_limit",
     *          type="integer",
     *          description="主题限制",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="deposit_time",
     *          type="integer",
     *          description="存款次数",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="total_deposit_money",
     *          type="integer",
     *          description="存款总额，历次存款额度",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="max_deposit_money",
     *          type="integer",
     *          description="最大存款金额",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_time",
     *          type="integer",
     *          description="提款次数",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_money",
     *          type="integer",
     *          description="提款总额",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="note",
     *          type="string",
     *          description="备注 ",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="is_auto_group",
     *          type="integer",
     *          description="用户自动分组，0:关闭， 1：启用 ",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",

     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/UserGroup"
     *
     *                  ),
     *          )
     *
     *   )
     *
     * )
     */
    public function add(Request $request)
    {
        $data=$request->all();
        $this->validator->with($data)->passesOrFail('add');
        $model = $this->repo->create($data);
        $response = [
            'message' => '数据创建成功.',
            'data'    => $model->toArray(),
        ];
        return $this->response()->array($response);
    }



    /**
     *
     *
     * @SWG\get(
     *      path="/api/userGroup",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="用户分组列表",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="array",
     *                       @SWG\Items(
     *                      type="object",
     *                      ref="#/definitions/UserGroup"
     *
     *                  ),
     *          )
     *      )
     *   )
     *
     * )
     */
    public function listGroup()
    {
        return $this->response()->paginator($this->repo->paginate(), new UserGroupTransformer());
    }




    /**
     *
     *
     * @SWG\get(
     *      path="/api/userGroup/{id}",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="获取单个用户分组详情",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
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
     *          description="查询分组ID",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *               @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/UserGroup"
     *
     *                  ),
     *          )
     *
     *   )
     *
     * )
     */
    public function get($id)
    {
        return $this->response()->item($this->repo->find($id), new UserGroupTransformer());
    }


    /**
     *
     *
     * @SWG\put(
     *      path="/api/userGroupStatus/{id}",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="修改用户分组的锁定状态",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
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
     *          description="修改分组id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *                @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/UserGroup"
     *
     *                  ),
     *          )
     *
     *   )
     *
     * )
     */
    public function updateStatus(Request $request,$id)
    {
        $data=$request->all();
        $data['id']=$id;
        $this->validator->with($data)->setId($id)->passesOrFail('updateStatus');
        $model=$this->repo->find($id);
        $model['is_auto_group']=$model['is_auto_group']==0?1:0;
        $model->save();

        $response = [
            'message' => '修改锁定成功',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }

    /**
     *
     *
     * @SWG\put(
     *      path="/api/userGroup/{id}",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="修改用户分组信息",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
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
     *          description="修改分组id",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="分组名称",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="begin_time",
     *          type="string",
     *          description="用户加入期间，开始时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="ent_time",
     *          type="string",
     *          description="用户加入期间，结束时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="theme_limit",
     *          type="integer",
     *          description="主题限制",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="deposit_time",
     *          type="integer",
     *          description="存款次数",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="total_deposit_money",
     *          type="integer",
     *          description="存款总额，历次存款额度",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="max_deposit_money",
     *          type="integer",
     *          description="最大存款金额",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_time",
     *          type="integer",
     *          description="提款次数",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_money",
     *          type="integer",
     *          description="提款总额",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="note",
     *          type="string",
     *          description="备注 ",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="is_auto_group",
     *          type="integer",
     *          description="用户自动分组，0:关闭， 1：启用 ",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",

     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/UserGroup"
     *
     *                  ),
     *          )
     *
     *   )
     *
     * )
     */
    public function update(Request $request, $id)
    {
        $this->validator->with($request->all())->setId($id)->passesOrFail('update');
        $model = $this->repo->update($request->all(), $id);

        $response = [
            'message' => 'test updated.',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }

    /**
     *
     *
     * @SWG\delete(
     *      path="/api/userGroup/{id}",
     *      tags={"admin-用户分组管理-songshu"},
     *      summary="删除用户分组信息",
     *      description="请求该接口需要先登录。",
     *      operationId="userGroup",
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
     *          description="修改分组id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *                @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="分组删除成功"
     *                  ),
     *          )
     *
     *   )
     *
     * )
     */
    public function delete($id)
    {
        $this->repo->delete($id);
        $response = [
            'message' => '分组删除成功.',
        ];
        return $this->response()->array($response);
    }




}