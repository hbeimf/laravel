<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use Illuminate\Http\Request;
use App\Transformer\AuditTransformer;
use App\Validator\AuditValidator;
use App\Repository\AuditRepository;

use App\Http\Model\Audit;
use App\Http\Model\OutMoney;

class  AuditController extends BaseController
{

    protected $validator;

    protected $repo;

    public function __construct(AuditRepository $_repo, AuditValidator $_validator)
    {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }
	
	
	/**
     * 手动取款action
     *
     * @SWG\Get(
     *      path="/v1/audit/get",
     *      tags={"admin-稽核费用详情-huang"},
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
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="用户名",
     *          required=true,
     *      ),
     *      @SWG\Response(
	 *          response="default",
	 *          description="返回该条链接的配置情况",
	 *			@SWG\Schema(
	 *              type="object",
	 *              @SWG\Property(
	 *                  property="de_audit",
	 *                  type="number",
	 *                  description="需要扣除的行政手续费"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="de_discoun",
	 *                  type="string",
	 *                  description="需要扣除的优惠"
	 *              ),
	 *				@SWG\Property(
	 *                  property="de_service_charge",
	 *                  type="string",
	 *                  description="需要扣除的手续费"
	 *              ),
	 *				@SWG\Property(
	 *                  property="de_overtime_charge",
	 *                  type="string",
	 *                  description="超过取款次数,需要扣除手续费"
	 *              )
	 *			),
	 *			@SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @SWG\Property(
     *                      property="money",
     *                      type="integer",
     *                      description="存款金额"
     *                  ),
     *                  @SWG\Property(
     *                      property="remit_money",
     *                      type="integer",
     *                      description="存款优惠"
     *                  ),
     *                  @SWG\Property(
     *                      property="audit",
     *                      type="integer",
     *                      description="优惠稽核标准"
     *                  ),
     *                  @SWG\Property(
     *                      property="audit_total",
     *                      type="integer",
     *                      description="有效投注"
     *                  ),
     *                  @SWG\Property(
     *                      property="server_removed",
     *                      type="integer",
     *                      description="优惠稽核标准（是否达标，0达标，1不达标）"
     *                  ),
     *                  @SWG\Property(
     *                      property="server_fee",
     *                      type="integer",
     *                      description="扣除服务费"
     *                  ),
     *                  @SWG\Property(
     *                      property="synthetical_audit",
     *                      type="integer",
     *                      description="常态稽核标准"
     *                  ),
     *                  @SWG\Property(
     *                      property="discount_removed",
     *                      type="integer",
     *                      description="常态稽核标准（是否达标，0达标，1不达标）"
     *                  ),
     *                  @SWG\Property(
     *                      property="remit_money",
     *                      type="integer",
     *                      description="扣除常态金额"
     *                  )
	 *				)
	 *			)
     *		)
     *	)
     */
    public function get(\Illuminate\Http\Request $request)
    {
		$name = $request->get('username');
		
		$Users = new \App\Http\Model\Users();
		$user = $Users->getUserByName($name);
		$Audits = new OutMoney();
		$info = $Audits->audit(1,$user->id);
		
        return $this->response()->array($info);
    }
	
}