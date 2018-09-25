<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
// use App\Http\Controllers\Admin\CommonController as BaseController;
use App\Repository\WithdrawMoneyConfigRepository;
use App\Transformer\WithdrawMoneyConfigTransformer;
use App\Validator\WithdrawMoneyConfigValidator;
use Illuminate\Http\Request;

class WithdrawmoneyController extends BaseController {

	protected $validator;

	protected $repo;
	
	protected $protectModel = ['线下取款模式', '人工取款模式'];

	public function __construct(WithdrawMoneyConfigRepository $_repo, WithdrawMoneyConfigValidator $_validator) {
		parent::__construct();
		$this->repo = $_repo;
		$this->validator = $_validator;

	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/withdrawMoney",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="取款模式列表 分页action",
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
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="free_time",
	 *                                   type="integer",
	 *                                   description="可免收N次手续费"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="service_charge",
	 *                                   type="integer",
	 *                                   description="否则每次收取收手续费N元"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="withdraw_money_time",
	 *                                   type="integer",
	 *                                   description="每日可取款次数"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_need_wait",
	 *                                   type="integer",
	 *                                   description="取款等待审核时间，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_money",
	 *                                   type="integer",
	 *                                   description="用户申请取款大等于wait_money元人民币时"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_hour",
	 *                                   type="integer",
	 *                                   description="则需要等待wait_hour小时审核时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="note",
	 *                                   type="string",
	 *                                   description="备注"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="created_at",
	 *                                   type="timestamp",
	 *                                   description="创建时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="updated_at",
	 *                                   type="timestamp",
	 *                                   description="更新时间"
	 *                               )
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
	 *				property="links",
	 *                 	       		@SWG\Items(
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
		return $this->response()->paginator($this->repo->paginate($limit), new WithdrawMoneyConfigTransformer());

	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/withdrawMoney/{id}",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="根据取款模式 { id } 获取单条取款模式action",
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
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="free_time",
	 *                                   type="integer",
	 *                                   description="可免收N次手续费"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="service_charge",
	 *                                   type="integer",
	 *                                   description="否则每次收取收手续费N元"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="withdraw_money_time",
	 *                                   type="integer",
	 *                                   description="每日可取款次数"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_need_wait",
	 *                                   type="integer",
	 *                                   description="取款等待审核时间，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_money",
	 *                                   type="integer",
	 *                                   description="用户申请取款大等于wait_money元人民币时"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_hour",
	 *                                   type="integer",
	 *                                   description="则需要等待wait_hour小时审核时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="note",
	 *                                   type="string",
	 *                                   description="备注"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="created_at",
	 *                                   type="timestamp",
	 *                                   description="创建时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="updated_at",
	 *                                   type="timestamp",
	 *                                   description="更新时间"
	 *                               )
	 *
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function get($id) {

		return $this->response()->item($this->repo->find($id), new WithdrawMoneyConfigTransformer());
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Post(
	 *      path="/v1/withdrawMoney",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="新增取款模式action",
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
	 *          description="模式名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="free_time",
	 *          type="integer",
	 *          description="可免收N次手续费",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="service_charge",
	 *          type="integer",
	 *          description="否则每次收取收手续费N元",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="withdraw_money_time",
	 *          type="integer",
	 *          description="每日可取款次数",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_need_wait",
	 *          type="integer",
	 *          description="取款等待审核时间，0：禁用， 1：启用",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="wait_money",
	 *          type="integer",
	 *          description="用户申请取款大等于wait_money元人民币时",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="wait_hour",
	 *          type="integer",
	 *          description="则需要等待wait_hour小时审核时间",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="max_money",
	 *          type="integer",
	 *          description="单次最高取款金额",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="min_money",
	 *          type="integer",
	 *          description="单次最低取款金额",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="note",
	 *          type="string",
	 *          description="备注",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="单条取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="free_time",
	 *                                   type="integer",
	 *                                   description="可免收N次手续费"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="service_charge",
	 *                                   type="integer",
	 *                                   description="否则每次收取收手续费N元"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="withdraw_money_time",
	 *                                   type="integer",
	 *                                   description="每日可取款次数"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_need_wait",
	 *                                   type="integer",
	 *                                   description="取款等待审核时间，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_money",
	 *                                   type="integer",
	 *                                   description="用户申请取款大等于wait_money元人民币时"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_hour",
	 *                                   type="integer",
	 *                                   description="则需要等待wait_hour小时审核时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="note",
	 *                                   type="string",
	 *                                   description="备注"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="created_at",
	 *                                   type="timestamp",
	 *                                   description="创建时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="updated_at",
	 *                                   type="timestamp",
	 *                                   description="更新时间"
	 *                               )
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
	 *      path="/v1/withdrawMoney/{id}",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="修改取款模式action",
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
	 *          description="模式名称",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="free_time",
	 *          type="integer",
	 *          description="可免收N次手续费",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="service_charge",
	 *          type="integer",
	 *          description="否则每次收取收手续费N元",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="withdraw_money_time",
	 *          type="integer",
	 *          description="每日可取款次数",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="is_need_wait",
	 *          type="integer",
	 *          description="取款等待审核时间，0：禁用， 1：启用",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="wait_money",
	 *          type="integer",
	 *          description="用户申请取款大等于wait_money元人民币时",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="wait_hour",
	 *          type="integer",
	 *          description="则需要等待wait_hour小时审核时间",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="max_money",
	 *          type="integer",
	 *          description="单次最高取款金额",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="min_money",
	 *          type="integer",
	 *          description="单次最低取款金额",
	 *          required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="note",
	 *          type="string",
	 *          description="备注",
	 *          required=true,
	 *      ),
	 *      @SWG\Response(
	 *          response=200,
	 *          description="单条取款模式",
	 *          @SWG\Schema(
	 *	   @SWG\Property(
	 *	      property="data",
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="free_time",
	 *                                   type="integer",
	 *                                   description="可免收N次手续费"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="service_charge",
	 *                                   type="integer",
	 *                                   description="否则每次收取收手续费N元"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="withdraw_money_time",
	 *                                   type="integer",
	 *                                   description="每日可取款次数"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_need_wait",
	 *                                   type="integer",
	 *                                   description="取款等待审核时间，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_money",
	 *                                   type="integer",
	 *                                   description="用户申请取款大等于wait_money元人民币时"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_hour",
	 *                                   type="integer",
	 *                                   description="则需要等待wait_hour小时审核时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="note",
	 *                                   type="string",
	 *                                   description="备注"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="created_at",
	 *                                   type="timestamp",
	 *                                   description="创建时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="updated_at",
	 *                                   type="timestamp",
	 *                                   description="更新时间"
	 *                               )
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
// 		$model = $this->repo->update($request->all(), $id);
		$params = $request->all();
		$row = $this->repo->find($id);
		
        if (in_array($row['name'], $this->protectModel)) {
             $params['name'] = $row['name']; 
        }
        $model = $this->repo->update($params, $id);
		
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
	 *      path="/v1/withdrawMoney/{id}",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="删除取款模式action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="withdrawMoney",
	 *      produces={"application/json"},
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=200,
	 *          description="单条取款模式",
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
	    
	    if (in_array($row['name'], $this->protectModel)) {
	        $response = [
	            'message' => '不能删除系统初始化取款模式.',
	        ];
	        return $this->response()->array($response);
	    }
	    
		$this->repo->delete($id);
		$response = [
			'message' => '删除取款模式成功.',
		];
		return $this->response()->array($response);
	}

	/**
	 * 取款模式列表action
	 *
	 * @SWG\Get(
	 *      path="/v1/withdrawMoneyAll",
	 *      tags={"admin-取款模式-maomao"},
	 *      summary="取款所有取款模式列表action",
	 *      description="请求该接口需要先登录。",
	 *      operationId="withdrawMoneyAll",
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
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="free_time",
	 *                                   type="integer",
	 *                                   description="可免收N次手续费"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="service_charge",
	 *                                   type="integer",
	 *                                   description="否则每次收取收手续费N元"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="withdraw_money_time",
	 *                                   type="integer",
	 *                                   description="每日可取款次数"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_need_wait",
	 *                                   type="integer",
	 *                                   description="取款等待审核时间，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_money",
	 *                                   type="integer",
	 *                                   description="用户申请取款大等于wait_money元人民币时"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="wait_hour",
	 *                                   type="integer",
	 *                                   description="则需要等待wait_hour小时审核时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低取款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="note",
	 *                                   type="string",
	 *                                   description="备注"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="created_at",
	 *                                   type="timestamp",
	 *                                   description="创建时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="updated_at",
	 *                                   type="timestamp",
	 *                                   description="更新时间"
	 *                               )
	 *     		)
	 *     	)

	 *                )
	 *	)
	 *      )
	 *   )
	 * )
	 */
	public function all() {
		return $this->response()->collection($this->repo->all(), new WithdrawMoneyConfigTransformer());
	}

}