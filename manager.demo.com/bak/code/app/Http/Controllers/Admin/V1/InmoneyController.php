<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Transformer\InmoneyTransformer;
use App\Validator\InmoneyValidator;
use App\Repository\InmoneyRepository;
use App\Http\Model\DepositConfig;
use Illuminate\Support\Facades\Auth;

use App\Http\Model\Inmoney;

class  InmoneyController extends BaseController
{

    protected $validator;

    protected $repo;

    public function __construct(InmoneyRepository $_repo, InmoneyValidator $_validator)
    {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }

    public function get($id)
    {
		
        return $this->response()->item($this->repo->find($id), new InmoneyTransformer());
    }


    /**
     * @param \Illuminate\Http\Request $request
     */
    public function create(Request $request)
    {
		
//        $this->validator->with($request->all())->passesOrFail('create');
		$Inmoneys = new Inmoney();
		
		$arr = $Inmoneys->getDeposit(2,100000,10110,FALSE);
		$Inmoneys->setRemit(100000);
		print_r($Inmoneys);
		$Inmoneys->add(38);
		
        $response = [
            'message' => '数据创建成功.',
            'data'    => $arr,
        ];
		
        return $this->response()->array($response);
    }


    /**
     * @param \Illuminate\Http\Request $request
     */
    public function updateTest(Request $request, $id)
    {

        $this->validator->with($request->all())->setId($id)->passesOrFail('update');
        $model = $this->repo->update($request->all(),$id);
        $response = [
            'message' => 'test updated.',
            'data' => $model->toArray()
        ];
        return $this->response()->array($response);

    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        $response = [
            'message' => '账号删除.',
        ];
        return $this->response()->array($response);
    }
	
	public function confirm($id)
    {
        $Inmoneys = new Inmoney();
		
		$item = $Inmoneys->find(12);
		$back = $item->confirm();
		
        $response = [
            'message' => '账号删除.',
        ];
        return $this->response()->array($response);
    }
	

	/**
	 * 人工存入
	 *
	 * @SWG\Post(
	 *      path="/vi/addInmoney",
	 *      tags={"admin-人工存入-wyg"},
	 *      summary="人工存入",
	 *      operationId="addInmoney",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
	 *			required=true,
     *          description="存款用户id",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="money",
     *          type="string",
	 *			required=true,
     *          description="存款金额",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="in_type",
     *          type="string",
	 *			required=true,
     *          description="存款模型名称，'人工存入', '负数额度归零', '活动优惠', '返点优惠', '人工存款'",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remit_money",
     *          type="string",
     *          description="汇款优惠金额，只有in_model等于1时才有",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="deposit_money",
     *          type="string",
     *          description="存款优惠金额，只有in_model等于1时才有",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remit_remark",
     *          type="string",
     *          description="汇款优惠备注",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="deposit_remark",
     *          type="string",
     *          description="存款优惠备注",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="remark",
     *          type="string",
     *          description="存款备注",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="synthetical_audit_set",
     *          type="integer",
     *          description="是否设置综合稽核，1是0否",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="synthetical_audit",
     *          type="integer",
     *          description="综合稽核打码量",
     *      ),
	 *      @SWG\Response(
	 *          response=400,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *				@SWG\Property(	
	 *					property="message",
	 *					type="integer", 
	 *					description="成功提示",
	 *				),
	 *			)
	 *		)
	 *	)
	 */
	public function addInmoney(Request $request) {
		$this->validator->with($request->all())->passesOrFail('add');
		$user = Auth::user();
		$data = $this->repo->addInmoney($request->all(), $user->id);
		if (isset($data['msg'])) {
			return $this->response()->array(['message' => $data['msg']]);
		}

		return $this->response()->array(['message' => '添加成功.']);
	}
	
	
	/**
	 * 人工存入的几种固定类型
	 *
	 * @SWG\Get(
	 *      path="/vi/manualDepositType",
	 *      tags={"admin-人工存入-wyg"},
	 *      summary="人工存入的几种固定类型",
	 *      operationId="manualDepositType",
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
	 *					description="模型名称",
	 *				),
	 *			)
	 *		)
	 *	)
	 */
	public function manualDepositType() {
		$data = Inmoney::$_personInMoney;
		return $this->response()->array(['message' => '返回成功', 'data' => $data]);
	}
	

	/**
	 * 根据存款金额获取优惠
	 *
	 * @SWG\Post(
	 *      path="/vi/getDiscount",
	 *      tags={"admin-人工存入-wyg"},
	 *      summary="ajax根据存款金额获取优惠",
	 *      operationId="getDiscount",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="money",
     *          type="string",
	 *			required=true,
     *          description="存入金额",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="uid",
     *          type="integer",
	 *			required=true,
     *          description="存款用户id",
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
	 *					property="discountMoney",
	 *					type="integer", 
	 *					description="优惠金额[存款优惠]",
	 *				),
	 *				@SWG\Property(	
	 *					property="exDiscountMoney",
	 *					type="integer", 
	 *					description="额外优惠金额[汇款优惠]",
	 *				),
	 *			)
	 *		)
	 *	)
	 */
	public function getDiscount(Request $request) {
		$modelId = 1; // 只计算人工存入模式
		if (! isset($request->uid) || ! isset($request->money)) {
			return $this->response()->array(['message' => '参数缺失']);
		}
		$depositModel = new DepositConfig();
		$inModel = $depositModel->where('id', $modelId)->first();
		$num = $this->repo->inmoneyCount($request->uid);
		$data['discountMoney'] = $this->repo->discount($inModel, $num, $request->money);
		$data['exDiscountMoney'] = $this->repo->discount($inModel, $num, $request->money, 'ex_');

		return $this->response()->array(['data' => $data, 'message' => '返回成功']);
	}
	

	/**
	 * 获取打码
	 *
	 * @SWG\Post(
	 *      path="/vi/getAudit",
	 *      tags={"admin-人工存入-wyg"},
	 *      summary="ajax根据金额获取常态打码量",
	 *      operationId="getAudit",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="money",
     *          type="integer",
	 *			required=true,
     *          description="存入金额",
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
	 *					property="audit",
	 *					type="integer", 
	 *					description="打码量",
	 *				),
	 *			)
	 *		)
	 *	)
	 */
	public function getAudit (Request $request) {
		$modelId = 1; // 只计算人工存入模式
		if (! isset($request->money)) {
			return $this->response()->array(['message' => '参数缺失']);
		}
		$depositModel = new DepositConfig();
		$deposit = $depositModel->where('id', $modelId)->first();
		list($audit, $fee) = $this->repo->normalAudit($request->money, $deposit);
		return $this->response()->array(['data' => ['audit' => $audit], 'message' => '返回成功']);
	}

}