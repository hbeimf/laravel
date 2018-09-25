<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\AdmissionRepository;
use App\Transformer\AdmissionTransformer;
use App\Validator\AdmissionValidator;
use Illuminate\Http\Request;
use App\Http\Model\Admission;
use Illuminate\Support\Facades\DB;

class AdmissionController extends BaseController
{
    protected $validator;

    protected $repo;


    public function __construct(AdmissionRepository $_repo, AdmissionValidator $_validator) {
        parent::__construct();
        $this->repo = $_repo;
        $this->validator = $_validator;

    }


    /**
     * @SWG\Definition(
     *     definition="admission",
     *                           @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="账户ID"
     *                  ),
     *                   @SWG\Property(
     *                      property="bank",
     *                      type="string",
     *                      description="银行名称"
     *                  ),
     *                   @SWG\Property(
     *                      property="number",
     *                      type="string",
     *                      description="银行账户号码"
     *                  ),
     *                   @SWG\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="开户人名称"
     *                  ),
     *                   @SWG\Property(
     *                      property="status",
     *                      type="integer",
     *                      description="状态：1.启用 2.停用"
     *                  ),
     *                   @SWG\Property(
     *                      property="url",
     *                      type="string",
     *                      description="收款二维码url"
     *                  ),
     *                   @SWG\Property(
     *                      property="province",
     *                      type="string",
     *                      description="开户银行省份"
     *                  ),
     *                   @SWG\Property(
     *                      property="city",
     *                      type="string",
     *                      description="开户银行城市"
     *                  ),
     *                   @SWG\Property(
     *                      property="sort",
     *                      type="integer",
     *                      description="排序"
     *                  ),
     *                   @SWG\Property(
     *                      property="group",
     *                      type="string",
     *                      description="分组"
     *
     *                  ),
     *                   @SWG\Property(
     *                      property="created_at",
     *                      type="string",
     *                      description="创建时间"
     *                  ),
     *                   @SWG\Property(
     *                      property="updated_at",
     *                      type="string",
     *                      description="更新时间"
     *                  ),
     *                   @SWG\Property(
     *                      property="group_names",
     *                      type="string",
     *                      description="账户所属分组"
     *                  ),
     *
     * )
     */


    /**
     * 查询线下收款账户列表
     *
     * @SWG\Get(
     *      path="/v1/admission",
     *      tags={"admin-收款账户-songshu"},
     *      summary="线下收款账户列表",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response=401,
     *          description="出错了",
     *     ),
     *@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="数组列表"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="array",
     *                       @SWG\Items(
     *                      type="object",
     *                      ref="#/definitions/admission"
     *
     *                  ),
     *          )
     *      )
     *   )
     *
     * )
     */
    public function listAdmission()
    {
        return $this->response()->paginator($this->repo->paginate(),new AdmissionTransformer());
    }



    /**
     *
     *
     * @SWG\get(
     *      path="/v1/admission/{id}",
     *      tags={"admin-收款账户-songshu"},
     *      summary="单个线下收款账户详情",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
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
     *          description="收款账户id",
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
     *                      description="单个线下收款用户详情"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/admission"
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
        return $this->response()->item($this->repo->find($id), new AdmissionTransformer());
    }



    /**
     *
     *
     * @SWG\post(
     *      path="/v1/admission",
     *      tags={"admin-收款账户-songshu"},
     *      summary="添加线下收款账户",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="bank",
     *          type="string",
     *          description="银行名称",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="number",
     *          type="integer",
     *          description="银行账户号码",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_name",
     *          type="string",
     *          description="开户人名称",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="bank_name",
     *          type="string",
     *          description="开户银行名称",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="province",
     *          type="string",
     *          description="开户行省份",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="city",
     *          type="string",
     *          description="开户行城市",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="group",
     *          type="string",
     *          description="分组",
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
     *                      description="单个线下收款用户详情"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/admission"
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
        $model = $this->repo->create($data);
        $response = [
            'message' => '数据创建成功.',
            'data'    => $model->toArray(),
        ];
        return $this->response()->array($response);
    }


    /**
     * 修改线下入款账户
     *
     * @SWG\Put(
     *      path="/v1/admission/{id}",
     *      tags={"admin-收款账户-songshu"},
     *      summary="修改线下入款账户",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="修改账户的ID",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="bank",
     *          type="string",
     *          description="银行名称",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="number",
     *          type="integer",
     *          description="微信账户",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_name",
     *          type="string",
     *          description="微信昵称",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="group",
     *          type="string",
     *          description="分组",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="url",
     *          type="string",
     *          description="二维码url",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了",
     *     ),
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *          @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="单个线下收款用户详情"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/admission"
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
        $model = $this->repo->update($request->all(), $id);

        $response = [
            'message' => 'test updated.',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }


    /**
     * 修改线下入款账户状态
     *
     * @SWG\Put(
     *      path="/v1/admissionStatus/{id}",
     *      tags={"admin-收款账户-songshu"},
     *      summary="修改线下入款账户状态",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="修改账户的ID",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="出错了",
     *     ),
     *	@SWG\Response(
     *          response=200,
     *          description="列表数组",
     *         @SWG\Items(
     *                  @SWG\Property(
     *                      property="message",
     *                      type="string",
     *                      description="单个线下收款用户详情"
     *                  ),
     *                  @SWG\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/definitions/admission"
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
        $model['status']=$model['status']==1?2:1;
        $model->save();

        $response = [
            'message' => '修改状态成功',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }


    /**
     * 删除线下入款账户
     *
     * @SWG\Delete(
     *      path="/v1/admission",
     *      tags={"admin-收款账户-songshu"},
     *      summary="删除线下入款账户",
     *      description="请求该接口需要先登录。",
     *      operationId="admission",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="删除账户的ID",
     *          required=true,
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="删除成功",
     *     )
     * )
     */
    public function delete(Request $request,$id)
    {
        $data=$request->all();
        $data['id']=$id;
        $this->validator->with($data)->setId($id)->passesOrFail('updateStatus');

        $this->repo->delete($id);
        $response = [
            'message' => '分组删除成功.',
        ];
        return $this->response()->array($response);
    }


}