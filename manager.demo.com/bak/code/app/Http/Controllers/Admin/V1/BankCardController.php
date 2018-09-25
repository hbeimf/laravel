<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\BankCardRepository as Repo;
use App\Transformer\BankCardTransformer;
use App\Validator\BankCardValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Model\BankCard;

class BankCardController extends BaseController
{
    protected $validator;
    protected $repo;

    public function __construct(Repo $_repo, BankCardValidator $_validator) {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }

    /**
     * 获取所选用户的银行卡
     *
     * @SWG\Get(
     *      path="/v1/bankcard/{uid}",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="获取用户的银行卡",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="uid",
     *          type="integer",
     *          description="用户id",
     *          required=true,
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
     *                      property="uid",
     *                      type="integer",
     *                      description="uid"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank_name",
     *                      type="string",
     *                      description="开户行"
     *                  ),
     *     @SWG\Property(
     *                      property="bank_area",
     *                      type="string",
     *                      description="开户地区"
     *                  ),
     *     @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="用户名"
     *                  ),
     *     @SWG\Property(
     *                      property="status",
     *                      type="int",
     *                      description="卡状态"
     *                  ),
     *     @SWG\Property(
     *                      property="is_default",
     *                      type="int",
     *                      description="是否默认卡 "
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
    public function listRecord($uid) {
        //$paginator = $this->repo->paginate();
        //return $this->response()->paginator($paginator, new BankCardTransformer());
        //$findByField = $this->repo->findByField();
        $data=$this->repo->findByField('uid',$uid);
        //return  response()->json($data);
        return  $this->response()->array(
            [
                'data' =>   $this->repo->findByField('uid',$uid)->toArray()
            ]);
    }

    /**
     * 给用户添加银行卡
     *
     * @SWG\Post(
     *      path="/v1/addBankcard",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="给用户添加银行卡",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="uid",
     *          type="integer",
     *          description="用户id",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bank_name",
     *          type="string",
     *          description="银行名称",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="card_num",
     *          type="string",
     *          description="银行卡号",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bank_area",
     *          type="string",
     *          description="银行地区",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bank_branch",
     *          type="string",
     *          description="所属支行",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="user_name",
     *          type="string",
     *          description="开户人姓名",
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
     *                      property="uid",
     *                      type="integer",
     *                      description="uid"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank_name",
     *                      type="string",
     *                      description="开户行"
     *                  ),
     *     @SWG\Property(
     *                      property="bank_area",
     *                      type="string",
     *                      description="开户地区"
     *                  ),
     *     @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="用户名"
     *                  ),
     *     @SWG\Property(
     *                      property="status",
     *                      type="int",
     *                      description="卡状态"
     *                  ),
     *     @SWG\Property(
     *                      property="is_default",
     *                      type="int",
     *                      description="是否默认卡 "
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
    public function addBankCard(Request $request) {
        $input = $request->all();
        $data = [
            'uid' =>$input['uid'],
            'bank_name' => $input['bank_name'],
            'card_num' =>$input['card_num'],
            'bank_area'=>$input['bank_area'],
            'bank_branch'=>$input['bank_branch'],
            'user_name'=>$input['user_name'],
//            'status'=>$input['status'],
//            'is_default'=>$input['is_default'],
        ];
//         $model = $this->repo->makeModel();
//         $model->uid = $request->get('uid');
//         $model->bank_name = $request->get('bank_name');
//         $model->card_num = $request->get('card_num');
//         $model->bank_area = $request->get('bank_area');
//         $model->bank_branch = $request->get('bank_branch');
//         $model->user_name = $request->get('user_name');
//         $model->status = $request->get('status');
//         $model->is_default = $request->get('is_default');
//         $model->save();
           $post = $this->repo->create( $data );
            $response = [
                'message' => '数据创建成功.',
                'row' =>$data,
            ];
            return $this->response()->array($response);
    }

    /**
     * 更新用户银行卡
     *
     * @SWG\Post(
     *      path="/v1/updateBankcard",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="更新用户银行卡",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="uid",
     *          type="integer",
     *          description="用户id",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bank_name",
     *          type="string",
     *          description="银行名称",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="card_num",
     *          type="string",
     *          description="银行卡号",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bank_area",
     *          type="string",
     *          description="银行地区",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="user_name",
     *          type="string",
     *          description="开户人姓名",
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
     *                      property="uid",
     *                      type="integer",
     *                      description="uid"
     *                  ),
     *                  @SWG\Property(
     *                      property="bank_name",
     *                      type="string",
     *                      description="开户行"
     *                  ),
     *     @SWG\Property(
     *                      property="bank_area",
     *                      type="string",
     *                      description="开户地区"
     *                  ),
     *     @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="用户名"
     *                  ),
     *     @SWG\Property(
     *                      property="status",
     *                      type="int",
     *                      description="卡状态"
     *                  ),
     *     @SWG\Property(
     *                      property="is_default",
     *                      type="int",
     *                      description="是否默认卡 "
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
    public function updateBankCard(Request $request) {
        $input = $request->all();
        $data = [
            'id' => $input['id'],
            'bank_name' => $input['bank_name'],
            'card_num' =>$input['card_num'],
            'bank_area'=>$input['bank_area'],
            'bank_branch'=>$input['bank_branch'],
            'user_name'=>$input['user_name'],
//            'status'=>$input['status'],
//            'is_default'=>$input['is_default'],
        ];

        $this->repo->update( $data, $input['id'] );
        $response = [
            'message' => '数据更新成功.',
            'row' =>$data,
        ];
        return $this->response()->array($response);
    }

    /**
     * 删除用户的银行卡
     *
     * @SWG\Post(
     *      path="/v1/deleteBankcard",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="删除用户的银行卡",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="id",
     *          type="integer",
     *          description="银行卡id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="message"
     *                  )
     *              )
     *         )
     *
     *
     *      )
     *   )
     * )
     */
    public function deleteBankCard(Request $request) {
        $input = $request->all();
//        $row = $this->repo->findByField('id',$input['id']);
//        $row_json = response()->json($row);
//        $row_arr = json_decode($row_json,true);

//        if(empty($row_arr)) {
//            $response = [
//                'message' => '记录不存在.',
//            ];
//            return $this->response()->array($response);
//        }
        $this->repo->delete( $input['id'] );
        $response = [
            'message' => '数据删除成功.',
        ];
        return $this->response()->array($response);
    }

    /**
     * 改变用户银行卡状态
     *
     * @SWG\Post(
     *      path="/v1/enableBankcard",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="更新用户银行卡状态(绑定，解绑  启用，禁用)",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="query",
     *          name="ids",
     *          type="string",
     *          description="需要更改状态的银行卡id(多张卡时用逗号分隔:（99,100）)",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="需要更改的银行卡状态(1:启用/绑定 2禁用/解绑  默认:1)",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="message"
     *                  )
     *              )
     *         )
     *
     *
     *      )
     *   )
     * )
     */
    public function enableBankCard(Request $request) {
        $input = $request->all();
        $ip_arr = explode(',',$input['ids']);
        $status = empty($input['status']) ? 1 : $input['status'];//默认为启用(1)
        foreach($ip_arr as $id){
            $data = ['status' => $status];
            $this->repo->update( $data, $id );
        }
        $response = [
            'message' => '数据更新成功.',
        ];
        return $this->response()->array($response);
    }


    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function getRecord($id) {

        return $this->response()->item($this->repo->with(['users'])->find($id), new BankCardTransformer());
    }

    /**
     * 获取我的银行卡
     *
     * @SWG\Get(
     *      path="/api/wallet/getCardList1",
     *      tags={"api-安全中心"},
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
            $uid = 92;

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

}