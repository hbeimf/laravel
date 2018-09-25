<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Http\Model\Users;
use App\Repository\OutmoneyRepository as Repo;
use App\Transformer\OutmoneyTransformer;
use App\Validator\OutmoneyValidator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\Model\OutMoney;
use App\Http\Model\WithdrawMoneyConfig;
use App\Http\Model\Audit;
use App\Http\Model\UserRelevantInfo;
use App\Http\Model\MoneyLog;


class OutmoneyController extends BaseController {

    protected $validator;

    protected $repo;

    public function __construct(Repo $_repo, OutmoneyValidator $_validator) {
        $this->repo = $_repo;
        $this->validator = $_validator;

    }



    /**
     * @SWG\Definition(
     *     definition="Outmoney",
     *     @SWG\Property(
     *         property="withdraw_money",
     *         type="integer",
     *         description="提现的金额"
     *     ),
     *     @SWG\Property(
     *         property="withdraw_type",
     *         type="string",
     *         description="提现的类型"
     *     ),
     *     @SWG\Property(
     *         property="withdraw_money_actual",
     *         type="integer",
     *         description="实际提现的金额",
     *     ),
     *     @SWG\Property(
     *         property="is_first",
     *         type="integer",
     *         description="是否首次出款,1:是，0：否",
     *     ),
     *     @SWG\Property(
     *         property="bank_name",
     *         type="string",
     *         description="出款银行",
     *     ),
     *     @SWG\Property(
     *         property="discount_removed",
     *         type="integer",
     *         description="优惠扣除，1:是，0：否； ",
     *     ),
     *     @SWG\Property(
     *         property="created_at",
     *         type="string",
     *         description="提交时间",
     *     ),
     *     @SWG\Property(
     *         property="updated_at",
     *         type="string",
     *         description="操作时间",
     *     ),
     * )
     *
     * @SWG\Definition(
     *     definition="OutmoneyDetail",
     *     @SWG\Property(
     *         property="username",
     *         type="string",
     *         description="会员账号"
     *     ),
     *     @SWG\Property(
     *         property="bank_name",
     *         type="string",
     *         description="银行名称"
     *     ),
     *     @SWG\Property(
     *         property="bank_username",
     *         type="string",
     *         description="银行账号姓名",
     *     ),
     *     @SWG\Property(
     *         property="card_num",
     *         type="string",
     *         description="银行账号",
     *     ),
     *     @SWG\Property(
     *         property="withdraw_money_actual",
     *         type="integer",
     *         description="实际出款金额",
     *     ),
     *     @SWG\Property(
     *         property="withdraw_status",
     *         type="integer",
     *         description="出款状态，0：未确认; -1：拒绝; 1：已发放;",
     *     ),
     *     @SWG\Property(
     *         property="level",
     *         type="string",
     *         description="会员等级,例如VIP1",
     *     ),
     *     @SWG\Property(
     *         property="total_money",
     *         type="integer",
     *         description="会员余额",
     *     ),
     *     @SWG\Property(
     *         property="daily_income",
     *         type="integer",
     *         description="每日盈余",
     *     ),
     *     @SWG\Property(
     *         property="daily_income_percent",
     *         type="string",
     *         description="每日盈率（%）",
     *     ),
     *     @SWG\Property(
     *         property="login_address",
     *         type="string",
     *         description="登入地址",
     *     ),
     *     @SWG\Property(
     *         property="bank_branch",
     *         type="string",
     *         description="银行地址",
     *     ),
     *     @SWG\Property(
     *         property="parent_username",
     *         type="string",
     *         description="上级账号",
     *     ),
     *     @SWG\Property(
     *         property="register_datetime",
     *         type="string",
     *         description="注册时间",
     *     ),
     *     @SWG\Property(
     *         property="remark",
     *         type="string",
     *         description="备注信息",
     *     ),
     * )
     *
     *
     * @SWG\Definition(
     *     definition="Pagination",
     *     @SWG\Property(
     *         property="pagination",
     *                  @SWG\Property(
     *                      property="total",
     *                      type="integer",
     *                      description="总条数"
     *                  ),
     *                  @SWG\Property(
     *                      property="count",
     *                      type="integer",
     *                      description="当前页条数"
     *                  ),
     *                  @SWG\Property(
     *                      property="per_page",
     *                      type="integer",
     *                      description="每页条数"
     *                  ),
     *                  @SWG\Property(
     *                      property="current_page",
     *                      type="integer",
     *                      description="当前页"
     *                  ),
     *                  @SWG\Property(
     *                      property="total_pages",
     *                      type="integer",
     *                      description="总页数"
     *                  )
     *     )
     * )
     *
     */

    /**
     * 获取出款列表action
     *
     * @SWG\Get(
     *      path="/v1/outmoney",
     *      tags={"admin-现金流-出款-tookit"},
     *      summary="获取出款列表.",
     *      description="获取出款列表",
     *      operationId="ListOutMoney",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="rangeField",
     *          type="string",
     *          description="用于范围查询的字段,例如: withdraw_money_actual",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="rangeMin",
     *          type="integer",
     *          description="范围查询的最大值",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="rangeMax",
     *          type="integer",
     *          description="范围查询的最小值,查询示例：rangField=withdraw_money_actual&rangeMin=100&rangeMax=1000",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="dateField",
     *          type="string",
     *          description="用于范围查询的字段,例如: created_at",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="dateMin",
     *          type="integer",
     *          description="unix timestamp, unix时间戳,例如：1536854400",
     *          required=false,
     *      ),
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="dateMax",
     *          type="integer",
     *          description="unix timestamp, unix时间戳；查询示例：dateField=created_at&dateMin=1536854400&dateMax=1536954400",
     *          required=false,
     *      ),
     *
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="search",
     *          type="string",
     *          description="模糊查询的关键字",
     *          required=false,
     *      ),
     *
     * 	    @SWG\Parameter(
     *          in="query",
     *          name="searchBy",
     *          type="string",
     *          description="模糊查询的字段，查询示例：search=admin&searchBy=user.name",
     *          required=false,
     *      ),
     *
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态,失败返回400状态",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/definitions/Outmoney"
     *              ),
     *              @SWG\Property(
     *                  property="meta",
     *                  type="object",
     *                  ref="#/definitions/Pagination"
     *              )
     *
     *          )
     *		)
     *	)
     */


    public function listRecord() {

        $paginator = $this->repo->with(['user'])->paginate();
        return $this->response()->paginator($paginator, new OutmoneyTransformer());

    }


    /**
     * 获取单个出款详情action
     *
     * @SWG\Get(
     *      path="/v1/outmoney/{id}",
     *      tags={"admin-现金流-出款-tookit"},
     *      summary="获取单个出款详情.",
     *      description="获取单个出款详情。",
     *      operationId="GetOutMoney",
     *      produces={"application/json"},
     *      security={
     *          {

     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/definitions/OutmoneyDetail"
     *              ),
     *          )
     *		),
     *      @SWG\Response(
     *          response=404,
     *          description="没有找到该条记录,返回404",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/Error404"
     *          )
     *		)
     *	)
     */

    public function getRecord($id) {

        $stdClass = $this->repo->getRecord($id);
        if($stdClass){
            $stdClass->level =  (is_numeric($stdClass->level_id)) ? 'VIP'.$stdClass->level_id : '';
            $stdClass->parent_username = ($stdClass->parent_uid)  ? Users::find($stdClass->parent_uid)->name() : '';
            return $this->response()->array([
                'data' => $stdClass
            ]);
        }else{
            return $this->response()->error('该记录未找到',404);
        }

    }

    /**
     * 手动取款action
     *
     * @SWG\Put(
     *      path="/v1/outmoney/{id}/confirm",
     *      tags={"admin-现金流-出款-tookit"},
     *      summary="手动出款.",
     *      description="相当于线下已经出款给客户了，这里只是手动确认下。。",
     *      operationId="confirmOutMoney",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="number",
     *          description="该条出款信息的id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态"
     *		)
     *	)
     */
    public function confirmRecord($id)
    {
        $model = $this->repo->find($id);
        if($model->status === 1)
        {
            return $this->response()->error('已出款，请不要重复提交。', 400);
        }

        if($this->unLocked($model, Auth::id()) === true)
        {
            $this->repo->confirm($id, $model->users->id);

            return $this->response()->array(
                [
                    'message' => '已确认手动出款',
                    'data' => $model
                ]
            );

        }

    }

    /**
     * 拒绝取款action
     *
     * @SWG\Put(
     *      path="/v1/outmoney/{id}/refuse",
     *      tags={"admin-现金流-出款-tookit"},
     *      summary="拒绝取款.",
     *      description="即提现扣款成功了，但是款项不会再返回给用户账户。 点击拒绝是要填入备注信息",
     *      operationId="RefuseOutMoney",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="number",
     *          description="该条出款信息的id",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remark",
     *          type="string",
     *          description="该条出款信息的备注信息",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态,失败返回400状态"
     *		)
     *	)
     */

    public function refuseRecord(Request $request, $id)
    {
        $model = $this->repo->find($id);
        $this->validator->with($request->all())->setId($id)->passesOrFail('refuse');
        if($this->unLocked($model, Auth::id()) === true)
        {
            $model->update([
                'status' => -1,
                'remark' => $request->get('remark'),
            ]);
            return $this->response()->array(
                [
                    'message' => '已拒绝此次提现',
                    'data' => $model
                ]
            );

        }

    }



    /**
     * @param OutMoney $model
     * @param integer $uid
     * @return bool|void
     */
    protected function unLocked($model,$uid)
    {
        if($model->isLock($uid) === false)
        {
            return $this->response()->error('该记录已被锁定。', 400);
        }
        return true;
    }


    /**
     * 出款备注action
     *
     * @SWG\Patch(
     *      path="/v1/outmoney/{id}/remark",
     *      tags={"admin-现金流-出款-tookit"},
     *      summary="出款备注.",
     *      description="填入备注信息",
     *      operationId="RemarkOutMoney",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="number",
     *          description="该条出款信息的id",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remark",
     *          type="string",
     *          description="该条出款信息的备注信息",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态,失败返回400状态"
     *		)
     *	)
     */
    public function updateRemark(Request $request, $id)
    {

        $model = $this->repo->find($id);
        $model->remark = $request->get('remark','');
        $model->save();
        return $this->response()->array(

            [
                'message' => '备注已更新',
                'data' => $model
            ]
        );

    }





    /**
     * 取款模式列表action
     *
     * @SWG\Post(
     *      path="/v1/outmoney/lock/{id}",
     *      tags={"admin-锁定一条出款记录-huang"},
     *      summary="锁定一条出款记录",
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
     *          name="id",
     *          type="number",
     *          description="该条出款信息的id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="正确返回200状态,失败返回405状态"
     *		)
     *	)
     */
    public function lock(Request $request, $id) {
        $uid = Auth::id();

        $this->validator->with(['id'=>$id])->setId($id)->passesOrFail('lock');

        $model = $this->repo->find($id);

        $back = $model->toLock($uid);

        if($back){
            return $this->response()->array(['message'=>'锁定成功!']);
        }else{
            return $this->response()->error('锁定失败!',405);
        }
    }

    /**
     * 取款模式列表action
     *
     * @SWG\Post(
     *      path="/v1/outmoney",
     *      tags={"admin-人工提出-maomao"},
     *      summary="人工提出action",
     *      description="请求该接口需要先登录。",
     *      operationId="outmoney",
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
     *          name="withdraw_type",
     *          type="string",
     *          description="提款类型",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="withdraw_money",
     *          type="integer",
     *          description="提出金额",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remark",
     *          type="string",
     *          description="备注",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="clear_audit",
     *          type="integer",
     *          description="是否清除常态稽核打码",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="clear_synthetical_audit",
     *          type="integer",
     *          description="是否清除综合稽核打码",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="单条取款模式",
     *          @SWG\Schema(
     *	   @SWG\Property(
     *	      property="data",
     *                              @SWG\Property(
     *                                   property="uid",
     *                                   type="int",
     *                                   description="uid"
     *                               ),
     *                               @SWG\Property(
     *                                   property="manager_id",
     *                                   type="int",
     *                                   description="操作员ID"
     *                               ),
     *
     *                               @SWG\Property(
     *                                   property="pay_scheme",
     *                                   type="string",
     *                                   description="MT:人工提出,MC:线下提现"
     *                               ),
     *                               @SWG\Property(
     *                                   property="withdraw_type",
     *                                   type="integer",
     *                                   description="提出类型"
     *                               ),
     *                               @SWG\Property(
     *                                   property="withdraw_money",
     *                                   type="integer",
     *                                   description="提出金额"
     *                               ),
     *                               @SWG\Property(
     *                                   property="is_first",
     *                                   type="integer",
     *                                   description="是否首次出款，1：是，0：不是"
     *                               ),
     *                               @SWG\Property(
     *                                   property="remark",
     *                                   type="string",
     *                                   description="备注"
     *                               ),
     *                               @SWG\Property(
     *                                   property="updated_at",
     *                                   type="integer",
     *                                   description="更新时间"
     *                               ),
     *                               @SWG\Property(
     *                                   property="created_at",
     *                                   type="integer",
     *                                   description="创建时间"
     *                               ),
     *                               @SWG\Property(
     *                                   property="id",
     *                                   type="integer",
     *                                   description="id"
     *                               ),
     *                               @SWG\Property(
     *                                   property="withdraw_number",
     *                                   type="integer",
     *                                   description="取款号"
     *                               )
     *
     *
     *     	)
     *                )
     *	)
     *      )
     *   )
     * )
     */
    public function createRecord(Request $request)
    {
        $this->validator->with($request->all())->passesOrFail('create');
        $req = $request->all();

        // 操作员ID
        $user = Auth::user();

        // 出款表
        $tableOutMoney = new OutMoney();
        $allcount = $tableOutMoney->where("uid",'=', $req['uid'])->count();

        $outmoney = [
            'uid' => $req['uid'], /// 用户ID
            'manage_id' =>$user->id, // 操作员ID
            'pay_scheme' => 'MT',   // MT:人工提出,MC:线下提现
            'withdraw_type' =>$req['withdraw_type'], /// 提出类型
            'withdraw_money' => $req['withdraw_money'], /// 提出金额
            'is_first' => ($allcount == 0) ? 1 : 0, // 是否首次出款 
            'remark' => $req['remark'], /// 备注
        ];

        $moneyLog = [
            'uid' => $req['uid'], // 用户id
            'manage_id' => $user->id, // 管理员操作id
            'in_out' => '1', // 1消耗，2增加
            'msg' =>'人工提取', // 金额变动信息
            'type_id' =>MoneyLog::TYPE_WITHDRAWAL_MONEY, // 流水类型id
            'money' =>$req['withdraw_money'], // 变动金额
            'balance' => '', // 余额
        ];

        $tableAudit = new Audit();
        $list_audit = $tableAudit->where('uid', '=', $req['uid'])->where('status', '=', 0)->get();

        foreach ($list_audit as $audit){
            // 清除常态稽核量
            if (isset($req['clear_audit'])) {
                $tableOutMoney->where('id', '=', $audit->in_id)->update(['audit'=>0]);
            }

            // 清除综合稽核打码量
            if (isset($req['clear_synthetical_audit'])) {
                $tableOutMoney->where('id', '=', $audit->in_id)->update(['synthetical_audit'=>0]);
            }
        }

        // 计算用户余额
        $tableUserRelevantInfo = new UserRelevantInfo();
        $UserRelevantInfo = $tableUserRelevantInfo->where('uid', '=', $req['uid'])->first();

        if ($UserRelevantInfo->total_money < $req['withdraw_money']){
            $response = [
                'message' => '账户余额不足',
            ];
            return $this->response()->array($response);
        } else {
            $moneyLog['balance'] = $UserRelevantInfo->total_money - $req['withdraw_money'];
        }

        $tableMoneyLog = new MoneyLog();
        // 更新数据   
        $model = $this->repo->create($outmoney);
        $tableMoneyLog->create($moneyLog);
        $tableUserRelevantInfo->where('uid', '=', $req['uid'])->update(['total_money'=> $moneyLog['balance']]);

        $response = [
            'message' => '人工提取创建成功.',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }


    /**
     * 人工提出的几种固定类型
     *
     * @SWG\Get(
     *      path="/vi/outmoneyType",
     *      tags={"admin-人工提出-maomao"},
     *      summary="人工提出的几种固定类型",
     *      operationId="outMoneyType",
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
     *					property="name",
     *					type="string",
     *					description="提出类型名称",
     *				),
     *			)
     *		)
     *	)
     */
    public function outmoneyType() {
        $data = [];
        foreach (OutMoney::$withdrawType as $v) {
            $data[] = [
//                 'id' =>$v,
                'name' => $v,
            ];
        }

        return $this->response()->array(['message' => '返回成功', 'data' => $data]);
    }


}