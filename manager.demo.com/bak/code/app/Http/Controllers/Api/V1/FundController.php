<?php
/**
 * 账户中心-金流
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/9/13
 * Time: 10:18
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\V1\Controller as BaseController;
use App\Http\Model\Admission;
use App\Http\Model\Inmoney;
use App\Http\Model\MoneyLog;
use App\Http\Model\OutMoney;
use App\Http\Model\UserInfo;
use App\Repository\MoneyLogRepository;
use App\Repository\OutmoneyRepository;
use App\Transformer\DepositConfigTransformer;
use App\Transformer\InmoneyTransformer;
use App\Transformer\MoneyLogTransformer;
use App\Transformer\OutmoneyTransformer;
use App\Validator\OutmoneyValidator;
use Illuminate\Http\Request;
use App\Http\Model\Flows;
use App\Http\Model\BankCard;
use App\Http\Model\Recharge;
use App\Http\Model\Account;
use App\Http\Model\Withdrawal;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Transformer\FundTransformer;
use App\Validator\FundValidator;
use App\Validator\InmoneyValidator;
use App\Repository\FundRepository;
use App\Repository\InmoneyRepository;
use App\Repository\DepositConfigRepository;
use App\Criteria\FundCriteria;


class FundController extends BaseController
{
    protected $validator;

    protected $repo;
    protected $drepo;

    const DEPOSIT_CONFIG_OFFLINE = 2;   //默认存款模式
    const WITHDRAW_MONEY_CONFIG = 2;          //默认取款模式

    public function __construct(FundRepository $_repo, InmoneyValidator $_validator, DepositConfigRepository $_drepo)
    {
        $this->repo = $_repo;
        $this->drepo = $_drepo;
        $this->validator = $_validator;

    }

    /*-------------------充值----------------------*/
    /**
     * 获取充值方式
     *
     * @SWG\Get(
     *      path="/v1/client/admission",
     *      tags={"h5-金流"},
     *      summary="获取充值方式",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="200",
     *          description="获取成功",
     *          @SWG\Schema(
     *				@SWG\Property(
     *					property="data",
     *						type="array",
     *						@SWG\Items(
     *                              @SWG\Property(
     *                                   property="type",
     *                                   type="integer",
     *                                   description="充值方式#0银行卡1微信2支付宝"
     *                               ),
     *						)
     *				)
     *			)
     *		)
     * )
     */
    public function listAdmission() {
        $this->repo->pushCriteria(FundCriteria::class);
        $posts = $this->repo->scopeQuery(function($query) {
            return $query->groupBy('type');
        })->get(['type']);
        /*$result = [
            'data'  => [
                'type' => array_column($posts, 'type'),
            ],
        ];
        return $result;*/
        return $this->response()->collection($posts, new FundTransformer());
    }

    /**
     * 获取充值方式下卡列表
     *
     * @SWG\Get(
     *      path="/v1/client/admission_child/{type}",
     *      tags={"h5-金流"},
     *      summary="获取充值方式下卡列表",
     *      description="请求该接口需要先登录。type传/v1/client/admission下返回的type",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
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
     *                                   type="integer",
     *                                   description="id"
     *                               ),
     *                               @SWG\Property(
     *                                   property="bank",
     *                                   type="string",
     *                                   description="名称"
     *                               ),
     *                               @SWG\Property(
     *                                   property="number",
     *                                   type="string",
     *                                   description="卡号"
     *                               ),
     *                               @SWG\Property(
     *                                   property="user_name",
     *                                   type="string",
     *                                   description="开户人名称"
     *                               ),
     *                               @SWG\Property(
     *                                   property="bank_name",
     *                                   type="string",
     *                                   description="开户行"
     *                               ),
     *						)
     *				)
     *			)
     *		)
     * )
     */
    public function listAdmissionChild($type) {
        $this->repo->pushCriteria(FundCriteria::class);
        $fields = [
            'id', 'bank', 'number', 'user_name', 'bank_name'
        ];
        return $this->response()->collection($this->repo->findByField('type', $type, $fields), new FundTransformer());
    }

    /**
     * 获取卡具体信息
     *
     * @SWG\Get(
     *      path="/v1/client/admission/{id}",
     *      tags={"h5-金流"},
     *      summary="获取卡具体信息",
     *      description="请求该接口需要先登录。id传/v1/client/admission_child/{type}下返回的id",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="200",
     *          description="获取成功",
     *          @SWG\Schema(
     *				@SWG\Property(
     *                                   property="id",
     *                                   type="integer",
     *                                   description="id"
     *                               ),
     *                               @SWG\Property(
     *                                   property="bank",
     *                                   type="string",
     *                                   description="名称"
     *                               ),
     *                               @SWG\Property(
     *                                   property="number",
     *                                   type="string",
     *                                   description="卡号"
     *                               ),
     *                               @SWG\Property(
     *                                   property="user_name",
     *                                   type="string",
     *                                   description="开户人名称"
     *                               ),
     *                               @SWG\Property(
     *                                   property="bank_name",
     *                                   type="string",
     *                                   description="开户行"
     *                               ),
     *                              @SWG\Property(
     *                                   property="url",
     *                                   type="string",
     *                                   description="微信支付宝二维码链接"
     *                               ),
     *			)
     *		)
     * )
     */
    public function getAdmission($id) {
        $this->repo->pushCriteria(FundCriteria::class);

        $fields = [
            'id', 'bank', 'number', 'user_name', 'bank_name', 'url'
        ];
        return $this->response()->item($this->repo->find($id, $fields), new FundTransformer());
    }

    /**
     * 获取充值区间
     *
     * @SWG\Get(
     *      path="/v1/client/deposit",
     *      tags={"h5-金流"},
     *      summary="获取充值区间",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="200",
     *          description="获取成功",
     *          @SWG\Schema(
     *				@SWG\Property(
     *                                   property="max_money",
     *                                   type="integer",
     *                                   description="最大值"
     *                               ),
     *                               @SWG\Property(
     *                                   property="min_money",
     *                                   type="integer",
     *                                   description="最小值"
     *                               ),
     *			)
     *		)
     * )
     */
    public function getDeposit() {
        $fields = ['max_money', 'min_money'];
        return $this->response()->item($this->drepo->find(self::DEPOSIT_CONFIG_OFFLINE, $fields), new DepositConfigTransformer());
    }

    /**
     * 提交充值
     *
     * @SWG\Post(
     *      path="/v1/client/inmoney",
     *      tags={"h5-金流"},
     *      summary="提交充值",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *			in="formData",
     *          name="pay_id",
     *          type="integer",
     *          description="存款账号id",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="formData",
     *          name="money",
     *          type="integer",
     *          description="存入金额",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="formData",
     *          name="nick",
     *          type="string",
     *          description="昵称",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="formData",
     *          name="audit",
     *          type="string",
     *          description="是否放弃优惠#1是0否",
     *			required=false,
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="成功",
     *		)
     * )
     */
    public function createInmoney(Request $request) {
        $uid = CommonController::mustNormalUser();
        $this->validator->with($request->all())->passesOrFail('c_create');

        $inmoney = new Inmoney();
        $preInfo = $inmoney->getDeposit(self::DEPOSIT_CONFIG_OFFLINE, $request->money, $uid, $request->audit);
        $id = $inmoney->recharge(self::DEPOSIT_CONFIG_OFFLINE, $request->nick);
        if($id) {
            $response = [
                'message' => '数据创建成功.',
            ];
        } else {
            $response = [
                'message' => '数据创建失败.',
            ];
        }

        return $this->response()->array($response);
    }

    /**
     * 充值优惠展示
     *
     * @SWG\Post(
     *      path="/v1/client/inmoney_pre",
     *      tags={"h5-金流"},
     *      summary="充值优惠展示",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Response(
     *          response=200,
     *          description="充值优惠",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="discount_proportion",
     *                      type="string",
     *                      description="存款优惠"
     *                  ),
     *                  @SWG\Property(
     *                      property="proportion",
     *                      type="string",
     *                      description="所需打码量"
     *                  ),
     *              ),
     *
     *          )
     *		)
     * )
     */
    public function getInmoneyPreferential(Request $request) {
        $uid = CommonController::mustNormalUser();
        $this->validator->with($request->all())->passesOrFail('c_preferential');
        $inmoney = new Inmoney();
        $preInfo = $inmoney->getDeposit(self::DEPOSIT_CONFIG_OFFLINE, $request->money, $uid, $request->audit);
        return $this->response()->array(['data' => $preInfo]);
    }

    /*-------------------充值end----------------------*/

    /*-------------------提现----------------------*/
    /**
     * 提现申请
     *
     * @SWG\Post(
     *      path="/v1/client/outmoney",
     *      tags={"h5-金流"},
     *      summary="提现申请",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *			in="formData",
     *          name="bank_card_id",
     *          type="integer",
     *          description="提现卡id",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="formData",
     *          name="withdraw_money",
     *          type="integer",
     *          description="提现金额",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="formData",
     *          name="money_password",
     *          type="string",
     *          description="交易密码",
     *			required=true,
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="成功",
     *		)
     * )
     */
    public function createOutmoney(Request $request, OutmoneyValidator $validator, OutmoneyRepository $repo) {
        //        $this->_checkOutmoney();
//        $uid = CommonController::mustNormalUser();
        $uid = 1;
        $validator->with($request->all())->passesOrFail('c_create');
        $input = $request->all();
        $input['uid'] = $uid;
        $input['pay_scheme'] = 'MC';
        $input['withdraw_type'] = '手动申请出款';
        $input['withdraw_config_id'] = self::WITHDRAW_MONEY_CONFIG;
        $model = $repo->create($input);

        $outMoney = new OutMoney();
        $preInfo = $outMoney->audit(self::WITHDRAW_MONEY_CONFIG, $uid);

        $response = [
            'message' => '数据创建成功.',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }

    /**
     *提现扣减展示
     * */
    /**
     * 提现扣减展示
     *
     * @SWG\Post(
     *      path="/v1/client/outmoney_pre",
     *      tags={"h5-金流"},
     *      summary="提现扣减展示",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Response(
     *          response=200,
     *          description="充值优惠",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="total_money",
     *                      type="string",
     *                      description="用户账户资金"
     *                  ),
     *                  @SWG\Property(
     *                      property="money",
     *                      type="string",
     *                      description="用户本次稽核能提现的费用"
     *                  ),
     *                  @SWG\Property(
     *                      property="de_audit",
     *                      type="string",
     *                      description="收取行政服务费"
     *                  ),
     *                  @SWG\Property(
     *                      property="de_discoun",
     *                      type="string",
     *                      description="扣除之前给的优惠"
     *                  ),
     *                  @SWG\Property(
     *                      property="de_overtime_charge",
     *                      type="string",
     *                      description="收取手续费"
     *                  ),
     *                   @SWG\Property(
     *                      property="free_time",
     *                      type="string",
     *                      description="可免费提现次数"
     *                  ),
     *              ),
     *
     *          )
     *		)
     * )
     */
    public function getOutmoneyPreferential(Request $request) {
//        $uid = CommonController::mustNormalUser();
        $uid = 52;
        $this->validator->with($request->all())->passesOrFail('c_preferential');
        $outmoney = new OutMoney();
        $preInfo = $outmoney->audit(self::WITHDRAW_MONEY_CONFIG, $uid, $request->money);
        return $this->response()->array(['data' => $preInfo]);
    }
    /*-------------------提现end----------------------*/


    /*-------------------交易明细----------------------*/
    /**
     * 交易明细
     *
     * @SWG\Get(
     *      path="/v1/client/money_log",
     *      tags={"h5-金流"},
     *      summary="交易明细",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *			in="query",
     *          name="flow_type",
     *          type="integer",
     *          description="明细类型#0账户明细；1充值明细；2提现明细",
     *			required=true,
     *      ),
     *     @SWG\Parameter(
     *			in="query",
     *          name="time",
     *          type="integer",
     *          description="时间#1今天；2昨天；3七天；4当月；5上月",
     *			required=true,
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="获取成功",
     *          @SWG\Schema(
     *				@SWG\Property(
     *					property="data",
     *						type="array",
     *						@SWG\Items(
     *                              @SWG\Property(
     *                                   property="money",
     *                                   type="integer",
     *                                   description="产生金额"
     *                               ),
     *                               @SWG\Property(
     *                                   property="_type_id",
     *                                   type="string",
     *                                   description="摘要"
     *                               ),
     *                               @SWG\Property(
     *                                   property="balance",
     *                                   type="integer",
     *                                   description="余额"
     *                               ),
     *                               @SWG\Property(
     *                                   property="created_at",
     *                                   type="string",
     *                                   description="添加时间"
     *                               ),
     *                               @SWG\Property(
     *                                   property="status",
     *                                   type="integer",
     *                                   description="状态#0未确认,-1禁止,1已发放"
     *                               ),
     *                               @SWG\Property(
     *                                   property="real_money",
     *                                   type="integer",
     *                                   description="实际到账金额"
     *                               ),
     *						)
     *				)
     *			)
     *		)
     * )
     */
    public function listMoneyLog(MoneyLogRepository $mrepo, InmoneyRepository $irepo, OutmoneyRepository $orepo) {
        $flowType = request('flow_type', 0);    //#0账户明细；1充值明细；2提现明细
        $this->_dealTime();
        if($flowType == 0) {
            return $this->_listMoneyLog($mrepo);
        }
        if($flowType == 1) {
            return $this->_listInmoney($irepo);
        }
        if($flowType == 2) {
            return $this->_listOutmoney($orepo);
        }
    }

    /**
     * 处理传入时间
     * */
    private function _dealTime() {
        $time = request('time', 1);
        switch ($time) {
            //今天
            case 1 :
                $this->nowTime = date('Y-m-d');
                $this->nexTime = date('Y-m-d', strtotime('+1 days'));
                break;
            //昨天
            case 2 :
                $this->nowTime = date('Y-m-d', strtotime('-1 days'));
                $this->nexTime = date('Y-m-d');
                break;
            //七天
            case 3 :
                $this->nowTime = date('Y-m-d', strtotime('-6 days'));
                $this->nexTime = date('Y-m-d', strtotime('+1 days'));
                break;
            //本月
            case 4 :
                $this->nowTime = date('Y-m-01');
                $this->nexTime = date('Y-m-d', strtotime('+1 days'));
                break;
            //上月
            case 5 :
                $this->nowTime = date('Y-m-01', strtotime('-1 month'));
                $this->nexTime = date('Y-m-01');
                break;
        }
    }

    /**
     * 账户明细
     * */
    private function _listMoneyLog($repo) {
        $limit = request('limit', 15);
        $columns = [
            'money', 'type_id', 'balance', 'created_at'
        ];
        $page = $repo->scopeQuery(function ($query) {
            return $query->where('created_at', '>=', $this->nowTime)
                ->where('created_at', '<', $this->nexTime);
        })->paginate($limit, $columns)->toArray();

        $pages = [];
        foreach($page['data'] as $k => $v) {
            switch ($v['type_id']) {
                case MoneyLog::TYPE_RECHARGE : $v['_type_id'] = '充值入款';break;
                case MoneyLog::TYPE_RECHARGE_DISCOUNTS : $v['_type_id'] = '存款优惠';break;
                case MoneyLog::TYPE_RECHARGE_DISCOUNTS_EXTRA : $v['_type_id'] = '额外优惠';break;
                case MoneyLog::TYPE_RECHARGE_DISCOUNTS_ARTIFICIAL : $v['_type_id'] = '人工存入';break;
                case MoneyLog::TYPE_WITHDRAWAL : $v['_type_id'] = '提现扣款';break;
                case MoneyLog::TYPE_WITHDRAWAL_MONEY : $v['_type_id'] = '人工提出';break;
            }
            $pages['data'][$k] = $v;
        }
        $pages['meta']['pagination'] = [
            'total' => $page['total'],
            'count' => $page['total'],
            'per_page' => $page['per_page'],
            'current_page' => $page['current_page'],
            'total_pages' => $page['last_page'],
            'links' => [],
        ];
        return response()->json($pages);
    }

    /**
     * 充值明细
     * */
    private function _listInmoney($repo) {
        $limit = request('limit', 15);
        $columns = [
            'money', 'status', 'remit_money', 'deposit_money', 'created_at', DB::raw('`money`+`remit_money`+`deposit_money` as real_money'),
        ];
        $page = $repo->scopeQuery(function ($query) {
            return $query->where('created_at', '>=', $this->nowTime)
                ->where('created_at', '<', $this->nexTime);
        })->paginate($limit, $columns);

        return $this->response()->paginator($page, new InmoneyTransformer());
    }

    /**
     *提现明细
     * */
    private function _listOutmoney($repo) {
        $limit = request('limit', 15);
        $columns = [
            'withdraw_money', 'withdraw_money_actual', 'status', 'created_at'
        ];
        $page = $repo->scopeQuery(function ($query) {
            return $query->where('created_at', '>=', $this->nowTime)
                ->where('created_at', '<', $this->nexTime);
        })->paginate($limit, $columns);

        return $this->response()->paginator($page, new OutmoneyTransformer());
    }
    /*-------------------资金流水end----------------------*/




    


}