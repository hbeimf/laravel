<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\BankListRepository as Repo;
use App\Transformer\BankListTransformer;
use App\Validator\BankListValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Model\BankCard;

class BankListController extends BaseController
{
    protected $validator;
    protected $repo;

    public function __construct(Repo $_repo, BankListValidator $_validator) {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }

    /**
     * 获取所有可选的银行列表
     *
     * @SWG\Get(
     *      path="/v1/bankList",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="获取可选银行列表",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *
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
     
     *              )
     *         )
     *
     *
     *      )
     *   )
     * )
     */
    public function listRecord() {
        //$paginator = $this->repo->paginate();
        //return $this->response()->paginator($paginator, new BankCardTransformer());
        //$findByField = $this->repo->findByField();
        //$data=$this->repo->findByField('uid',$uid);
        //$data=$this->repo->all();
        //return  response()->json($data);
        return  $this->response()->array(
            [
                'data' =>   $this->repo->all()->toArray()
            ]);
    }

    /**
     * 添加可选银行
     *
     * @SWG\Post(
     *      path="/v1/addBank",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="添加银行",
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
    public function addBank(Request $request) {
        $input = $request->all();
        $data = [
            'name' => $input['name'],
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
     * 更新可选银行
     *
     * @SWG\Post(
     *      path="/v1/updateBank",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="更新银行",
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
    public function updateBank(Request $request) {
        $input = $request->all();
        $data = [
            'id' => $input['id'],
            'name' => $input['name'],
        ];

        $this->repo->update( $data, $input['id'] );
        $response = [
            'message' => '数据更新成功.',
            'row' =>$data,
        ];
        return $this->response()->array($response);
    }

    /**
     * 删除可选银行
     *
     * @SWG\Post(
     *      path="/v1/deleteBank",
     *      tags={"admin-用户银行卡管理-zyh"},
     *      summary="删除银行",
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
    public function deleteBank(Request $request) {
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


}