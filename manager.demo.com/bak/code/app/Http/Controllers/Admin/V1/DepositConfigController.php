<?php
/**
 * 出款控制
 */
namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Transformer\DepositConfigTransformer;
use App\Validator\DepositConfigValidator;
use App\Repository\DepositConfigRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class  DepositConfigController extends BaseController
{
	
    protected $validator;

    protected $repo;
	
    public function __construct(DepositConfigRepository $_repo, DepositConfigValidator $_validator) {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }
	
	/**
	 * 存款模式列表
	 * @return type
	 * 
	 * @SWG\Get(
	 *      path="/v1/deposit",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="存款模式列表",
	 *      operationId="deposit",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(
	 *					property="data",
	 *						type="array",
	 *						@SWG\Items(
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
	 *                                   property="update_at",
	 *                                   type="integer",
	 *                                   description="更新时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="can_del",
	 *                                   type="integer",
	 *                                   description="是否可以删除，1可删除，0不可删除"
	 *                               ),
	 *						)
	 *				),
	 *				@SWG\Property(
	 *					property="meta",
	 *						@SWG\Property(
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
	 *									property="links",
	 *										@SWG\Items(
	 *                 	             			@SWG\Property(
	 *                 	                             property="previous",
	 *                 	                             type="string",
	 *                 	                             description="上一页链接 ,如果是首页，则无此属性"
	 *                 	                        ),
	 *                 	             			@SWG\Property(
	 *                 	                             property="next",
	 *                 	                             type="string",
	 *                 	                             description="下一页链接 ，如果是最后一页，则无此属性"
	 *                 	                        )
	 *										)
	 *                 	              )
	 *							 )
	 *					)
	 *				)
	 *      )
	 *   )
	 * )
	 */
    public function depositList () {
        return $this->response()->paginator($this->repo->scopeQuery(
			function($query){
				return $query->selectRaw('id, name, updated_at,can_del')->orderBy('id','asc');
			}
		)->paginate(),new DepositConfigTransformer());
    }
	

	/**
	 * 所有存款模式列表【不分页】
	 * @return type
	 * 
	 * @SWG\Get(
	 *      path="/v1/allDeposit",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="所有存款模式列表【不分页】",
	 *      operationId="allDeposit",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(
	 *					property="data",
	 *						type="array",
	 *						@SWG\Items(
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
	 *                                   property="update_at",
	 *                                   type="integer",
	 *                                   description="更新时间"
	 *                               )
	 *						)
	 *				)
	 *			)
	 *		)
	 * )
	 */
    public function allDeposit () {
        return $this->response()->collection($this->repo->all(['id', 'name']), new DepositConfigTransformer());
    }
	
	/**
	 * 获取单个模型
	 * @param type $id
	 * @return type
	 * @SWG\Get(
	 *      path="/v1/getDeposit/{id}",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="获取单个模型详情",
	 *      operationId="getDeposit",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="string",
     *          description="跟在域名地址/v1/getDeposit/后面，如/v1/getDeposit/1",
     *          required=true,
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(
	 *					property="data",
	 *						type="array",
	 *						@SWG\Items(
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                              @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_type",
	 *                                   type="integer",
	 *                                   description="存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_time",
	 *                                   type="integer",
	 *                                   description="存款优惠次数，前discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_giveup",
	 *                                   type="integer",
	 *                                   description="用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_money",
	 *                                   type="integer",
	 *                                   description="单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_proportion",
	 *                                   type="integer",
	 *                                   description="优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_max_money",
	 *                                   type="integer",
	 *                                   description="单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_type",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_time",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数，前ex_discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_giveup",
	 *                                   type="integer",
	 *                                   description="额外用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_money",
	 *                                   type="integer",
	 *                                   description="额外单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_proportion",
	 *                                   type="integer",
	 *                                   description="额外优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_max_money",
	 *                                   type="integer",
	 *                                   description="额外单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_enable",
	 *                                   type="integer",
	 *                                   description="常态性稽核打码量是否启用，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="proportion",
	 *                                   type="integer",
	 *                                   description="要求用户提款时达到上次取款后的每次当时存款额度的proportion%的打码量"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="relaxable",
	 *                                   type="integer",
	 *                                   description="额外可放宽"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="administrative_rate",
	 *                                   type="integer",
	 *                                   description="否则收取行政费率"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_is_enable",
	 *                                   type="integer",
	 *                                   description="普通优惠稽核设定，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="code_checking",
	 *                                   type="string",
	 *                                   description="综合打码量稽核"
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
	 *                                   description="修改时间"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="can_del",
	 *                                   type="integer",
	 *                                   description="1可删除，0不可删除"
	 *                               ),
	 *						)
	 *				)
	 *			)
	 *		)
	 * )
	 */
	public function getDeposit ($id) {
		return $this->response()->item($this->repo->find($id), new DepositConfigTransformer());
	}
	

	/**
	 * 添加一个存款模型
	 * @return type
	 * @SWG\Post(
	 *      path="/v1/deposit/add",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="添加一个存款模型",
	 *      operationId="deposit/add",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="模式名称",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="max_money",
	 *          type="integer",
	 *          description="单次最高存款金额",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="min_money",
	 *          type="integer",
	 *          description="单次最低存款金额",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_type",
	 *          type="integer",
	 *          description="存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_time",
	 *          type="integer",
	 *          description="存款优惠次数，前discount_time次"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_giveup",
	 *          type="integer",
	 *          description="用户是否可放弃优惠，0：否， 1：是"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_money",
	 *          type="integer",
	 *          description="单次存款达到多少进行优惠"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_proportion",
	 *          type="integer",
	 *          description="优惠比例"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_max_money",
	 *          type="integer",
	 *          description="单次优惠上限金额"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_type",
	 *          type="integer",
	 *          description="额外存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_time",
	 *          type="integer",
	 *          description="额外存款优惠次数，前ex_discount_time次"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_giveup",
	 *          type="integer",
	 *          description="额外用户是否可放弃优惠，0：否， 1：是"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_money",
	 *          type="integer",
	 *          description="额外单次存款达到多少进行优惠"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_proportion",
	 *          type="integer",
	 *          description="额外优惠比例"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_max_money",
	 *          type="integer",
	 *          description="额外单次优惠上限金额"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="常态性稽核打码量是否启用，0：禁用， 1：启用",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="proportion",
	 *          type="integer",
	 *          description="要求用户提款时达到上次取款后的每次当时存款额度的proportion%的打码量"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="relaxable",
	 *          type="integer",
	 *          description="额外可放宽"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="administrative_rate",
	 *          type="integer",
	 *          description="否则收取行政费率"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_is_enable",
	 *          type="integer",
	 *          description="普通优惠稽核设定，0：禁用， 1：启用",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="code_checking",
	 *          type="string",
	 *          description="综合打码量稽核"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="note",
	 *          type="string",
	 *          description="备注"
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(
	 *					property="data",
	 *						type="array",
	 *						@SWG\Items(
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                              @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_type",
	 *                                   type="integer",
	 *                                   description="存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_time",
	 *                                   type="integer",
	 *                                   description="存款优惠次数，前discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_giveup",
	 *                                   type="integer",
	 *                                   description="用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_money",
	 *                                   type="integer",
	 *                                   description="单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_proportion",
	 *                                   type="integer",
	 *                                   description="优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_max_money",
	 *                                   type="integer",
	 *                                   description="单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_type",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_time",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数，前ex_discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_giveup",
	 *                                   type="integer",
	 *                                   description="额外用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_money",
	 *                                   type="integer",
	 *                                   description="额外单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_proportion",
	 *                                   type="integer",
	 *                                   description="额外优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_max_money",
	 *                                   type="integer",
	 *                                   description="额外单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_enable",
	 *                                   type="integer",
	 *                                   description="常态性稽核打码量是否启用，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="proportion",
	 *                                   type="integer",
	 *                                   description="要求用户提款时达到上次取款后的每次当时存款额度的proportion%的打码量"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="relaxable",
	 *                                   type="integer",
	 *                                   description="额外可放宽"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="administrative_rate",
	 *                                   type="integer",
	 *                                   description="否则收取行政费率"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_is_enable",
	 *                                   type="integer",
	 *                                   description="普通优惠稽核设定，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="code_checking",
	 *                                   type="string",
	 *                                   description="综合打码量稽核"
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
	 *                                   description="修改时间"
	 *                               )
	 *						)
	 *				)
	 *			)
	 *		)
	 * )
	 */
	public function addDeposit(Request $request) {
		$this->validator->with($request->all())->passesOrFail('create');
		$model = $this->repo->create($request->all());
		$response = [
			'message' => '数据创建成功.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);
	}
	

	/**
	 * 修改存款模型
	 * @return type
	 * @SWG\Post(
	 *      path="/v1/deposit/update/{id}",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="修改存款模型",
	 *      operationId="update",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="id",
	 *          type="integer",
	 *          description="要修改的存款模式，跟在url后面，如/deposit/update/{id}",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="模式名称",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="max_money",
	 *          type="integer",
	 *          description="单次最高存款金额",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="min_money",
	 *          type="integer",
	 *          description="单次最低存款金额",
	 *			required=true,
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_type",
	 *          type="integer",
	 *          description="存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_time",
	 *          type="integer",
	 *          description="存款优惠次数，前discount_time次"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_giveup",
	 *          type="integer",
	 *          description="用户是否可放弃优惠，0：否， 1：是"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_money",
	 *          type="integer",
	 *          description="单次存款达到多少进行优惠"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_proportion",
	 *          type="integer",
	 *          description="优惠比例"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="discount_max_money",
	 *          type="integer",
	 *          description="单次优惠上限金额"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_type",
	 *          type="integer",
	 *          description="额外存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_time",
	 *          type="integer",
	 *          description="额外存款优惠次数，前ex_discount_time次"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_giveup",
	 *          type="integer",
	 *          description="额外用户是否可放弃优惠，0：否， 1：是"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_money",
	 *          type="integer",
	 *          description="额外单次存款达到多少进行优惠"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_proportion",
	 *          type="integer",
	 *          description="额外优惠比例"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_discount_max_money",
	 *          type="integer",
	 *          description="额外单次优惠上限金额"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="is_enable",
	 *          type="integer",
	 *          description="常态性稽核打码量是否启用，0：禁用， 1：启用",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="proportion",
	 *          type="integer",
	 *          description="要求用户提款时达到上次取款后的每次当时存款额度的proportion%的打码量"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="relaxable",
	 *          type="integer",
	 *          description="额外可放宽"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="administrative_rate",
	 *          type="integer",
	 *          description="否则收取行政费率"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="ex_is_enable",
	 *          type="integer",
	 *          description="普通优惠稽核设定，0：禁用， 1：启用",
	 *			required=true
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="code_checking",
	 *          type="string",
	 *          description="综合打码量稽核"
	 *      ),
	 *      @SWG\Parameter(
	 *			in="formData",
	 *          name="note",
	 *          type="string",
	 *          description="备注"
	 *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(
	 *					property="data",
	 *						type="array",
	 *						@SWG\Items(
	 *                              @SWG\Property(
	 *                                   property="id",
	 *                                   type="int",
	 *                                   description="id"
	 *                               ),
	 *                              @SWG\Property(
	 *                                   property="name",
	 *                                   type="string",
	 *                                   description="模式名称"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="max_money",
	 *                                   type="integer",
	 *                                   description="单次最高存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="min_money",
	 *                                   type="integer",
	 *                                   description="单次最低存款金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_type",
	 *                                   type="integer",
	 *                                   description="存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_time",
	 *                                   type="integer",
	 *                                   description="存款优惠次数，前discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_giveup",
	 *                                   type="integer",
	 *                                   description="用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_money",
	 *                                   type="integer",
	 *                                   description="单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_proportion",
	 *                                   type="integer",
	 *                                   description="优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="discount_max_money",
	 *                                   type="integer",
	 *                                   description="单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_type",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_time",
	 *                                   type="integer",
	 *                                   description="额外存款优惠次数，前ex_discount_time次"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_giveup",
	 *                                   type="integer",
	 *                                   description="额外用户是否可放弃优惠，0：否， 1：是"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_money",
	 *                                   type="integer",
	 *                                   description="额外单次存款达到多少进行优惠"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_proportion",
	 *                                   type="integer",
	 *                                   description="额外优惠比例"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_discount_max_money",
	 *                                   type="integer",
	 *                                   description="额外单次优惠上限金额"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="is_enable",
	 *                                   type="integer",
	 *                                   description="常态性稽核打码量是否启用，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="proportion",
	 *                                   type="integer",
	 *                                   description="要求用户提款时达到上次取款后的每次当时存款额度的proportion%的打码量"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="relaxable",
	 *                                   type="integer",
	 *                                   description="额外可放宽"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="administrative_rate",
	 *                                   type="integer",
	 *                                   description="否则收取行政费率"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="ex_is_enable",
	 *                                   type="integer",
	 *                                   description="普通优惠稽核设定，0：禁用， 1：启用"
	 *                               ),
	 *                               @SWG\Property(
	 *                                   property="code_checking",
	 *                                   type="string",
	 *                                   description="综合打码量稽核"
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
	 *                                   description="修改时间"
	 *                               )
	 *						)
	 *				)
	 *			)
	 *		)
	 * )
	 */
	public function updateDeposit(Request $request, $id) {
		$this->validator->with($request->all())->setId($id)->passesOrFail('update');
		$model = $this->repo->update($request->all(), $id);
		$response = [
			'message' => '更新成功.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);
	}
	
	/**
	 * 删除存款模式
	 * 
	 * @SWG\Delete(
	 *      path="/v1/deposit/delete/{id}",
	 *      tags={"admin-存款模型-wyg"},
	 *      summary="删除存款模式",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
	 *			required=true,
     *          description="要删除的存款模式id，跟在url后面",
     *      ),
	 *      @SWG\Response(
	 *          response=400,
	 *          description="删除异常",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="删除成功",
	 *		)
	 *	)
	 */
	public function delete($id) {
		$ret = $this->repo->deleteWhere(['id' => $id,'can_del' => 1]);
		if ($ret) {
			$response = ['message' => '删除成功.'];
		} else {
			$response = ['message' => '删除失败.'];
		}
		return $this->response()->array($response);
	}
	
}