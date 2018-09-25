<?php
/**
 * 用户控制器
 */
namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Transformer\UserInfoTransformer;
use App\Validator\UserInfoValidator;
use App\Repository\UserInfoRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class  UserInfoController extends BaseController
{
	
    protected $validator;

    protected $repo;
	
    public function __construct(UserInfoRepository $_repo, UserInfoValidator $_validator) {
        $this->repo = $_repo;
        $this->validator = $_validator;
    }
	
	/**
	 * 获取用户部分资料
	 *
	 * @SWG\Post(
	 *      path="/vi/userInfo",
	 *      tags={"admin-人工存入-wyg"},
	 *      summary="获取用户部分资料",
	 *      operationId="userInfo",
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
     *          description="要查询的用户id",
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
	 *					property="parentDir",
	 *					type="string", 
	 *					description="用户代理层级信息",
	 *				),
	 *				@SWG\Property(	
	 *					property="status",
	 *					type="string", 
	 *					description="用户状态信息",
	 *				),
	 *				@SWG\Property(	
	 *					property="totalMoney",
	 *					type="string", 
	 *					description="用户余额",
	 *				),
	 *			)
	 *		)
	 *	)
	 */
	public function getUser(Request $request) {
		$data = $this->repo->with(['UserRelevantInfo'])->findByField('uid', $request->uid)->toArray();
		if (empty($data)) {
			return ['data' => [], 'code' => 401, 'msg' => '找不到用户'];
		}
		if ( ! empty($data[0]['parent_uid_dir'])) {
			$parntDir = preg_replace('/;/', '/', $data[0]['parent_uid_dir']);
			$dir = substr($parntDir, 0, strlen($parntDir)-1);
		} else {
			$dir = "";
		}
		$ret = [
			'parentDir' => "厅主".(empty($dir) ? "" : "/".$dir),
			'status' => $this->repo->getUserStatus(),
			'totalMoney' => isset($data[0]['user_relevant_info']['total_money']) ? $data[0]['user_relevant_info']['total_money'] : 0,
		];
		return response()->json(['data' => [$ret], 'code' => 200]);
	}
}