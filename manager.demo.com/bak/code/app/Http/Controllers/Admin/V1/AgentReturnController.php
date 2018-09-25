<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;

use App\Repository\AgentReturnRepository;
use App\Transformer\AgentReturnTransformer;
use App\Validator\AgentReturnValidator;
use Illuminate\Http\Request;
use App\Http\Model\AgentReturn;

class  AgentReturnController extends BaseController
{
    protected $validator;

    protected $repo;

    //模糊查询
    public function __construct(AgentReturnRepository $_repo, AgentReturnValidator $_validator) {
        parent::__construct();
        $this->repo = $_repo;
        $this->validator = $_validator;

    }


    /**
     * 查询禁止代理返点列表
     *
     * @SWG\Get(
     *      path="/api/agentRebate",
     *      tags={"admin-禁止代理返点-songshu"},
     *      summary="获取禁止代理返点列表",
     *      description="请求该接口需要先登录。",
     *      operationId="agentRebate",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *       @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
     *          description="用户ID",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="note",
     *          type="string",
     *          description="备注",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="created_at",
     *          type="string",
     *          description="创建时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="updated_at",
     *          type="string",
     *          description="修改时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="获取禁止代理返点ID",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="array",
     *                     @SWG\Items(
     *                           @SWG\Property(
     *                              property="uid",
     *                              type="int",
     *                              description="用户ID"
     *                             ),
     *                            @SWG\Property(
     *                              property="note",
     *                              type="string",
     *                              description="添加备注"
     *                             ),
     *                            @SWG\Property(
     *                              property="created_at",
     *                              type="timestamp",
     *                              description="创建时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="updated_at",
     *                              type="timestamp",
     *                              description="修改时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="id",
     *                              type="int",
     *                              description="禁用代理列表ID"
     *                             ),
     *                  @SWG\Property(
     *                      property="user_info",
     *                      type="array",
     *                     @SWG\Items(
     *                           @SWG\Property(
     *                              property="nickname",
     *                              type="string",
     *                              description="用户名称"
     *                             ),
     *                          )
     *                          )
     *                      )
     *                  ),
     *          )
     *      )
     *   )
     *
     * )
     */
    public function listAgentReturn()
    {
        return $this->response()->paginator($this->repo->with(['userInfo'])->paginate(), new AgentReturnTransformer());
    }


    /**
     * 添加禁止代理返点
     *
     * @SWG\Post(
     *      path="/api/agentRebate",
     *      tags={"admin-禁止代理返点-songshu"},
     *      summary="添加禁止代理返点",
     *      description="请求该接口需要先登录。",
     *      operationId="agentRebate",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *       @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="用户uid",
     *          required=true,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="note",
     *          type="string",
     *          description="备注",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="created_at",
     *          type="string",
     *          description="创建时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="updated_at",
     *          type="string",
     *          description="修改时间",
     *          required=false,
     *      ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="获取禁止代理返点ID",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="出错了",
     *     ),
     *
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数据创建成功"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *
     *                           @SWG\Property(
     *                              property="uid",
     *                              type="int",
     *                              description="用户ID"
     *                             ),
     *                            @SWG\Property(
     *                              property="note",
     *                              type="string",
     *                              description="添加备注"
     *                             ),
     *                            @SWG\Property(
     *                              property="created_at",
     *                              type="timestamp",
     *                              description="创建时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="updated_at",
     *                              type="timestamp",
     *                              description="修改时间"
     *                             ),
     *                            @SWG\Property(
     *                              property="id",
     *                              type="int",
     *                              description="禁用代理列表ID"
     *                             ),
     *
     *                  ),
     *          )
     *      )
     *   )
     *
     * )
     */
    public function addAgentRebate(Request $request)
    {
        $this->validator->with($request->all())->passesOrFail('create');
        $model = $this->repo->create($request->all());
        $response = [
            'message' => '数据创建成功.',
            'data'    => $model->toArray(),
        ];
        return $this->response()->array($response);
    }

    /**
     * 删除禁止代理返点
     *
     * @SWG\Delete(
     *      path="/api/agentRebate/{id}",
     *      tags={"admin-禁止代理返点-songshu"},
     *      summary="删除禁止代理返点",
     *      description="请求该接口需要先登录。",
     *      operationId="agentRebate",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *       @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="禁止代理返点列表id",
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
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="移除禁止代理返点列表成功"
     *                  ),
     *          )
     *      )
     *   )
     *
     * )
     */
    public function deleteAgentRebate($id)
    {
        $this->repo->delete($id);
        $response = [
            'message' => '账号删除.',
        ];
        return $this->response()->array($response);
    }
}