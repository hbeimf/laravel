<?php
/**
 * 账户中心-安全中心
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/9
 * Time: 11:34
 */

namespace App\Http\Controllers\Api;

use App\Http\Model\BankList;
use App\Http\Model\UserInfo;
use Illuminate\Http\Request;
use App\Http\Model\Flows;
use App\Http\Model\BankCard;
use App\Http\Model\Recharge;
use App\Http\Model\Account;
use App\Http\Model\Withdrawal;
use Illuminate\Support\Facades\DB;
use Validator;

class WalletController extends CommonController
{
    /**
     * 获取我的银行卡
     *
     * @SWG\Get(
     *      path="/api/wallet/getCardList",
     *      tags={"h5-安全中心"},
     *      summary="获取我的银行卡",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="limit",
     *          type="integer",
     *          description="每页数据量",
     *          default=20,
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="page",
     *          type="integer",
     *          description="页码",
     *          default=1,
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank_name",
     *                      type="string",
     *                      description="开户行"
     *                  ),
     *                  @SWG\Property(
     *                      property="card_num",
     *                      type="integer",
     *                      description="卡号"
     *                  )
     *              )
     *         )
     *
     *
     *      )
     *   )
     * )
     */
    public function getCardList()
    {
        try {
            $this->log->debug("获取我的银行卡！！");
            $uid = CommonController::mustNormalUser();

            $limit = request('limit', 20);
            $page = request('page', 1);
            $result = BankCard::where(['uid' => $uid, 'status' => BankCard::STATUS_YES])->orderByDesc('id')->paginate($limit, ['id', 'bank_name', 'card_num', 'is_default'], 'page', $page)->toArray();

            $result['data'] = array_map(function($arr) {
                $arr['card_num'] = str_pad(substr($arr['card_num'], -4), strlen($arr['card_num']), "*", STR_PAD_LEFT);
                return $arr;
            }, $result['data']);
            $this->result['data'] = $result;

        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 获取支持银行列表
     * */
    /**
     * 获取支持银行列表
     *
     * @SWG\Get(
     *      path="/api/wallet/getBankList",
     *      tags={"h5-安全中心"},
     *      summary="获取支持银行列表",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="default",
     *          description="列表",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="银行"
     *                  ),
     *              )
     *         )
     *
     *
     *      )
     *   )
     * )
     */
     public function getBankList() {
         $this->result['data'] = BankList::all();
         return response()->json($this->result);
     }

    /**
     * 绑定银行卡
     *
     * @SWG\Post(
     *      path="/api/wallet/bindCard",
     *      tags={"h5-安全中心"},
     *      summary="绑定银行卡",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="bank_name",
     *          type="string",
     *          description="开户银行",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_name",
     *          type="string",
     *          description="真实姓名",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="card_num",
     *          type="string",
     *          description="银行卡号",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="bank_area",
     *          type="string",
     *          description="所属省\市",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="bank_branch",
     *          type="string",
     *          description="开户支行",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="交易密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function bindCard(Request $request)
    {
        try {
            $this->log->debug("绑定银行卡！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'bank_name' => 'required',
                'user_name' => 'required',
                'card_num' => 'required',
                'bank_area' => 'required',
                'bank_branch' => 'required',
                'password' => 'required',
            ];
            $input = $request->all();
            $this->validator = Validator::make($input, $rule);
            $this->validatorFails();

            $userInfo = UserInfo::find($uid, ['money_password']);
            if($userInfo && $userInfo->money_password) {
                $this->_checkPassword($input['password']);
            } else {
                //设置交易密码
                UserInfo::where('uid', $uid)->update(['money_password' => bcrypt($request->password)]);
            }
            $bankCard = BankCard::where(['uid' => $uid, 'card_num' => $input['card_num']])->first();
            if(!$bankCard) {
                $data = [
                    'bank_name' => $input['bank_name'],
                    'user_name' => $input['user_name'],
                    'card_num' => $input['card_num'],
                    'bank_area' => $input['bank_area'],
                    'bank_branch' => $input['bank_branch'],
                    'uid' => $uid,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                BankCard::insert($data);
            } else {
                $bankCard->bank_name = $input['bank_name'];
                $bankCard->user_name = $input['user_name'];
                $bankCard->card_num = $input['card_num'];
                $bankCard->bank_area = $input['bank_area'];
                $bankCard->bank_branch = $input['bank_branch'];
                $bankCard->status = BankCard::ISDEFAULT_YES;
                $bankCard->save();
            }

        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 设置默认银行卡
     *
     * @SWG\Post(
     *      path="/api/wallet/setDefaultCard",
     *      tags={"h5-安全中心"},
     *      summary="设置默认银行卡",
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
     *          description="卡id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function setDefaultCard(Request $request) {
        try {
            $this->log->debug("设置默认银行卡！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'id' => 'required|integer',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();
            $cardInfo = $this->_checkCardId($request->id);
            if($uid != $cardInfo->uid) {
                throw new \Exception('网络繁忙', 4002);
            }
            BankCard::where('uid', $uid)->update(['is_default' => BankCard::ISDEFAULT_NO]);
            $cardInfo->is_default = BankCard::ISDEFAULT_YES;
            $cardInfo->save();
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 校验交易密码
     *
     * @SWG\Post(
     *      path="/api/wallet/checkPassword",
     *      tags={"h5-安全中心"},
     *      summary="校验交易密码",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="交易密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="操作成功",
     *      )
     * )
     */
    public function checkPassword(Request $request) {
        try {
            $this->log->debug("校验交易密码！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'password' => 'required',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();
            $this->_checkPassword($request->password);

        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 校验交易密码
     * */
    private function _checkPassword($password) {
        $uid = CommonController::getUid();
        $userInfo = UserInfo::find($uid, ['money_password']);
        if(!$userInfo || !$userInfo->money_password) {
            throw new \Exception('交易密码未设置', 1003);
        }
        $moneyPassword = $userInfo->money_password;
        if (!\Hash::check($password, $moneyPassword)) { //原始密码和数据库里的密码进行比对
            //交易密码连续错误5次锁定
            $this->_FailPwdNum();
            throw new \Exception('交易密码错误', 1001);
        } else {
            cache()->forget( config('cache-key.TRADING_PWD_FAIL_NUM').$uid );
        }
    }

    /**
     * 删除银行卡
     *
     * @SWG\Post(
     *      path="/api/wallet/delCard",
     *      tags={"h5-安全中心"},
     *      summary="删除银行卡",
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
     *          description="卡id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function delCard(Request $request)
    {
        try {
            $this->log->debug("删除银行卡！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'id' => 'required|integer',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();
            $cardInfo = $this->_checkCardId($request->id);
            if($uid != $cardInfo->uid) {
                throw new \Exception('网络繁忙', 4002);
            }
            $cardInfo->status = BankCard::STATUS_NO;
            $cardInfo->save();

        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 判断是否已设置交易密码
     * */
    /**
     * 判断是否已设置交易密码
     *
     * @SWG\Get(
     *      path="/api/wallet/isSetTradinglPwd",
     *      tags={"h5-安全中心"},
     *      summary="判断是否已设置交易密码",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="default",
     *          description="状态",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="status",
     *                  type="integer",
     *                  description="状态（#0：未设置；1：已设置）"
     *              ),
     *
     *        )
     *     )
     * )
     */
    public function isSetTradinglPwd() {
        try {
            $this->log->debug("判断是否已设置交易密码！！");
            $uid = CommonController::mustNormalUser();
            $userInfo = UserInfo::find($uid, ['money_password']);

            if(empty($userInfo->money_password)) {
                $result['is_set']   = 0;
            } else {
                $result['is_set']   = 1;
            }
            $this->result['data'] = $result;

        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 设置交易密码
     *
     * @SWG\Post(
     *      path="/api/wallet/setTradinglPwd",
     *      tags={"h5-安全中心"},
     *      summary="设置交易密码",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="提现密码",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="c_password",
     *          type="string",
     *          description="确认提现密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function setTradinglPwd(Request $request)
    {
        try {
            $this->log->debug("设置提现密码！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'password' => 'required|min:6',
                'c_password' => 'required|same:password',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();

            UserInfo::where('uid', $uid)->update(['money_password' => bcrypt($request->password)]);
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 更改交易密码
     *
     * @SWG\Post(
     *      path="/api/wallet/changeTradinglPwd",
     *      tags={"h5-安全中心"},
     *      summary="更改交易密码",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="oldpassword",
     *          type="string",
     *          description="原提现密码",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="提现密码",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="c_password",
     *          type="string",
     *          description="确认提现密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function changeTradinglPwd(Request $request)
    {
        try {
            $this->log->debug("更改交易密码！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'oldpassword' => 'required|min:6',
                'password' => 'required|min:6',
                'c_password' => 'required|min:6|same:password',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();
            $this->_checkPassword($request->oldpassword);

            UserInfo::where('uid', $uid)->update(['money_password' => bcrypt($request->password)]);
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 充值
     * */
    public function recharge(Request $request)
    {
        try {
            $this->log->debug("充值！！");
            $uid = CommonController::mustNormalUser();
            $rule = [
                'amount' => 'required|numeric',
                'channel' => 'required|integer',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();

            $input = $request->all();
            //充值渠道 #1微信 2支付宝 3人工
            switch (request('channel', 0)) {
                case Recharge::CHANNEL_ALIPAY:

                    break;
                case Recharge::CHANNEL_WECHAT:

                    break;
                case Recharge::CHANNEL_OFFLINE:
                    $input['status'] = Recharge::STATUS_PENDING;
                    break;
                default:
                    throw new \Exception('充值渠道有误', 4000);
            }

            $input['uid'] = $uid;
            $input['add_time'] = time();
            Recharge::insert($input);
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 提现
     * */
    public function withdrawal(Request $request)
    {
        try {
            $uid = CommonController::mustNormalUser();
            $rule = [
                'amount' => 'required|numeric',
                'card_id' => 'required|integer',
            ];
            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();

            $input = $request->all();
            //校验卡是否正确
            $cardInfo = $this->checkCardId($input['card_id']);

            //校验账户余额是否足够
            $this->checkUserBalance($uid, $input['amount']);

            $input['uid'] = $uid;
            $input['bank_card_info'] = $cardInfo['uid'] . '-' . $cardInfo['real_name'] . '-' . $cardInfo['account_number'];
            //入库操作
            DB::transaction(function () use ($input) {
                $accountData = [
                    'balance' => 'balance' - $input['amount'],
                    'amount_frozen' => 'amount_frozen' + $input['amount'],
                ];
                DB::table(test)->increment('num', 1, ['vote' => DB::raw('`vote`+1')]);
                Account::where('uid', $input['uid'])->update($accountData);
                $withdrawalData = [
                    'uid' => $input['uid'],
                    'amount' => $input['amount'],
                    'bank_card_id' => $input['card_id'],
                    'bank_card_info' => $input['bank_card_info'],
                    'add_time' => time(),
                ];
//                Account::where('uid', $input['uid'])->update($accountData);
                Withdrawal::insert($withdrawalData);
            });
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     *资金流水
     * */
    public function flows()
    {
        try {
            $uid = CommonController::getUid();
            $flows = new Flows();
            $list = $flows->getListByUid($uid, 2);
            $this->result['data'] = $list;
        } catch (\Exception $e) {
            $this->result = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 校验卡id
     * */
    protected function _checkCardId($cardId)
    {
        $bankCard = new BankCard();
        $cardInfo = $bankCard->getCardById($cardId, ['id', 'uid', 'user_name', 'card_num']);
        if (empty($cardInfo)) {
            throw new \Exception('请选择正确的数据', 4002);
        }
        if ($cardInfo->uid !== CommonController::getUid()) {
            throw new \Exception('请选择正确的数据', 4003);
        }
        return $cardInfo;
    }

    /**
     * 校验交易密码连续错误次数
     * */
    private function _FailPwdNum() {
        $maxNum = 5;
        $uid = CommonController::getUid();
        $key = config('cache-key.TRADING_PWD_FAIL_NUM').$uid;
        if(cache()->has( $key ) == false) {
            cache()->forever($key, 1);
        }

        //提现密码连续输错5次,将该账户锁定1小时不能验证提现密码
        if(cache( $key ) > $maxNum) {
            throw new \Exception('账户已被锁定，请在3600秒后再试', 1004);
        }
        $nowNum = cache()->pull( $key );
        if($nowNum >= $maxNum) {
            cache()->put($key, $nowNum + 1, 60);
        } else {
            cache()->forever($key, $nowNum + 1);
        }
    }

    /**
     *
     * */


    /**
     * 校验账户余额是否足够
     * */
    protected function checkUserBalance($uid, $amount)
    {
        $account = new Account();
        $userAccount = $account->getAccountByUid($uid, ['balance']);
        if (empty($userAccount)) {
            throw new \Exception('余额不足', 4004);
        }
        if ($amount > $userAccount->balance) {
            throw new \Exception('余额不足', 4005);
        }
    }


}