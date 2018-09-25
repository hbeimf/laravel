<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\IcomeRepository;
use Illuminate\Http\Request;
use App\Transformer\IcomeTransformer;

class IcomeController extends BaseController
{
    protected $repo;

    public function __construct(IcomeRepository $_repo)
    {
        $this->repo = $_repo;
    }

    /**
     * 线下入款列表
     *
     * @SWG\Get(
     *      path="/v1/icome/list",
     *      tags={"admin-线下入款列表-zcp"},
     *      summary="线下入款列表",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="0未确认,-1已取消,1已存入",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="money_start",
     *          type="integer",
     *          description="金额开始",
     *          required=false,
     *      ),
     *		@SWG\Parameter(
     *          in="formData",
     *          name="money_end",
     *          type="integer",
     *          description="金额结尾",
     *          required=false,
     *      ),
     *		@SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="用户账号",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_group",
     *          type="string",
     *          description="用户分组",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="time_type",
     *          type="string",
     *          description="时间类型，0表示全部，1表示提交时间，2表示操作时间",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="time_start",
     *          type="string",
     *          description="开始时间，比如2018-09-01",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="time_end",
     *          type="string",
     *          description="结束时间，比如2018-09-12",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="数组格式",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="id",
     *                          type="integer",
     *                          description="订单id"
     *                      ),
     *                      @SWG\Property(
     *                          property="user_name",
     *                          type="string",
     *                          description="用户账号"
     *                      ),
     *                      @SWG\Property(
     *                          property="group_name",
     *                          type="string",
     *                          description="用户分组"
     *                      ),
     *                      @SWG\Property(
     *                          property="group_id",
     *                          type="integer",
     *                          description="用户组ID"
     *                      ),
     *                      @SWG\Property(
     *                          property="manage_name",
     *                          type="string",
     *                          description="操作员账号"
     *                      ),
     *                      @SWG\Property(
     *                          property="bank",
     *                          type="string",
     *                          description="存款开户行"
     *                      ),
     *                      @SWG\Property(
     *                          property="number",
     *                          type="string",
     *                          description="存款银行账号"
     *                      ),
     *                      @SWG\Property(
     *                          property="money",
     *                          type="integer",
     *                          description="存款金额"
     *                      ),
     *                      @SWG\Property(
     *                          property="accout_name",
     *                          type="string",
     *                          description="存款人"
     *                      ),
     *                      @SWG\Property(
     *                          property="created_at",
     *                          type="string",
     *                          description="提交时间"
     *                      ),
     *                      @SWG\Property(
     *                          property="updated_at",
     *                          type="string",
     *                          description="操作时间"
     *                      )
     *                  )
     *               ),
     *               @SWG\Property(
	 *		            property="meta",
	 *	                    @SWG\Property(
	 *                      property="pagination",
	 *                 	           @SWG\Property(
	 *                 	               property="total",
	 *                 	               type="int",
	 *                 	               description="总条数"
	 *                 	            ),
	 *                 	            @SWG\Property(
	 *                 	               property="count",
	 *                 	               type="int",
	 *                 	               description="当前页条数"
	 *                 	            ),
	 *                 	            @SWG\Property(
	 *                 	               property="per_page",
	 *                 	               type="int",
	 *                 	               description="每页条数"
	 *                 	            ),
	 *                 	            @SWG\Property(
	 *                 	               property="current_page",
	 *                 	               type="int",
	 *                 	               description="当前第几页"
	 *                 	            ),
	 *                 	            @SWG\Property(
	 *                 	               property="total_pages",
	 *                 	               type="int",
	 *                 	               description="总页数"
	 *                 	            ),
	 *                 	            @SWG\Property(
	 *				                   property="links",
	 *                                 @SWG\Property(
	 *                 	                   property="previous",
	 *                 	                   type="string",
	 *                 	                   description="上一页链接 ,如果是首页，则无此属性"
	 *                 	               ),
	 *                 	               @SWG\Property(
	 *                 	                   property="next",
	 *                 	                   type="string",
	 *                 	                   description="下一页链接 ，如果是最后一页，则无此属性"
	 *                 	               )
	 *                 	            )
	 *                       )
	 *                   )
     *              )
     *   )
     * )
     */
    public function getList(Request $request) {
        $status = request('status');
        $name = request('name');
        $money_start = request('money_start');
        $money_end = request('money_end');
        $user_group = request('user_group');
        $time_type = request('time_type');
        $time_start = request('time_start');
        $time_end = request('time_end');
        $limit = request('limit');
        $page = $this->repo->get_list($status,$name,$money_start,$money_end,$user_group,$time_type,$time_start,$time_end,$limit)->paginate($limit);
        return $this->response()->paginator($page, new IcomeTransformer());
    }
    
    /**
     * 确认订单
     *
     * @SWG\post(
     *      path="/v1/icome/ok",
     *      tags={"admin-线下入款列表-zcp"},
     *      summary="确认订单",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="订单id",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="opt_id",
     *          type="integer",
     *          description="操作员id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="返回当前修改行的数据，json格式",
     *          @SWG\Schema(
     *              @SWG\Property(
     *              property="data",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="订单id"
     *                  ),
     *                  @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="用户账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="group_name",
     *                      type="string",
     *                      description="用户分组"
     *                  ),
     *                  @SWG\Property(
     *                      property="group_id",
     *                      type="integer",
     *                      description="用户组ID"
     *                  ),
     *                  @SWG\Property(
     *                      property="manage_name",
     *                      type="string",
     *                      description="操作员账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank",
     *                      type="string",
     *                      description="存款开户行"
     *                  ),
     *                  @SWG\Property(
     *                      property="number",
     *                      type="string",
     *                      description="存款银行账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="money",
     *                      type="integer",
     *                      description="存款金额"
     *                  ),
     *                  @SWG\Property(
     *                      property="accout_name",
     *                      type="string",
     *                      description="存款人"
     *                  ),
     *                  @SWG\Property(
     *                      property="created_at",
     *                      type="string",
     *                      description="提交时间"
     *                  ),
     *                  @SWG\Property(
     *                      property="updated_at",
     *                      type="string",
     *                      description="操作时间"
     *                  )
     *      ))
     *   )
     * )
     */
    public function okOrder(Request $request) {
        $inmoney = $this->repo->getModel();
        $inmoney = $inmoney->find($request->input('id'));
        $list = $inmoney->ok_order($request->all());
        $response = [
            'message' => '确认订单',
            'data'    => $list,
        ];
        return $this->response()->array($response);
    }
    
    /**
     * 取消订单
     *
     * @SWG\post(
     *      path="/v1/icome/cancel",
     *      tags={"admin-线下入款列表-zcp"},
     *      summary="取消订单",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="订单id",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="opt_id",
     *          type="integer",
     *          description="操作员id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="返回当前修改行的数据，json格式",
     *          @SWG\Schema(
     *              @SWG\Property(
     *              property="data",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="订单id"
     *                  ),
     *                  @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="用户账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="group_name",
     *                      type="string",
     *                      description="用户分组"
     *                  ),
     *                  @SWG\Property(
     *                      property="group_id",
     *                      type="integer",
     *                      description="用户组ID"
     *                  ),
     *                  @SWG\Property(
     *                      property="manage_name",
     *                      type="string",
     *                      description="操作员账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank",
     *                      type="string",
     *                      description="存款开户行"
     *                  ),
     *                  @SWG\Property(
     *                      property="number",
     *                      type="string",
     *                      description="存款银行账号"
     *                  ),
     *                  @SWG\Property(
     *                      property="money",
     *                      type="integer",
     *                      description="存款金额"
     *                  ),
     *                  @SWG\Property(
     *                      property="accout_name",
     *                      type="string",
     *                      description="存款人"
     *                  ),
     *                  @SWG\Property(
     *                      property="created_at",
     *                      type="string",
     *                      description="提交时间"
     *                  ),
     *                  @SWG\Property(
     *                      property="updated_at",
     *                      type="string",
     *                      description="操作时间"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function cancelOrder(Request $request) {
        $inmoney = $this->repo->getModel();
        $list = $inmoney->cancel_order($request->all());
        $response = [
            'message' => '取消订单',
            'data'    => $list,
        ];
        return $this->response()->array($response);
    }

}