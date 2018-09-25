<?php
/**
 * 推广链接
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/9
 * Time: 11:34
 */
namespace App\Http\Controllers\Admin;

use App\Helper\Fn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Http\Model\PromoteConfig;	//推广链接模型
use App\Http\Model\ReturnPoint;		//返点链接对应点数
use App\Http\Model\Users;
use App\Http\Model\UserInfo;
use App\Http\Model\CeilPoint;		//返点模型
use App\Http\Model\Domain;		//返点模型


class PromoteController extends CommonController
{
	
	
	/**
	 * 获取 需要编辑的部分的详情
	 * 如果提交了ID，表示是编辑，信息已经存在，
	 * 如果没有提交id，那么，提交name，模拟一份和已经添加的数据返回。
	 * 如果用户是一个普通会员，返回不让添加提示，因为普通会员不能创建推广链接
	 * 检查下max_point 的数据，如果全部为0，也不可以设置推广链接
	 * 
	 * @SWG\Get(
	 *      path="/api/admPromote/details",
	 *      tags={"admin-推广链接-huang"},
	 *      summary="获取推广链接的返点和配置信息",
	 *      operationId="getMyData",
	 *		security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Parameter(
	 *          in="formData",
	 *          name="id",
	 *          type="number",
	 *          description="选填，该条配置的id，在/api/admPromote/list中获取，如果不提交id，表示是新增一条推广链接",
	 *          required=false,
	 *      ),
	 *		@SWG\Parameter(
	 *          in="formData",
	 *          name="name",
	 *          type="string",
	 *          description="完整的用户名，选填，如果没有提交id，就需要提交用户名",
	 *          required=false,
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="返回该条链接的配置情况",
	 *          @SWG\Schema(
	 *              type="object",
	 *              @SWG\Property(
	 *                  property="id",
	 *                  type="number",
	 *                  description="该配置项的id"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="name",
	 *                  type="string",
	 *                  description="完整用户名"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="status",
	 *                  type="string",
	 *                  description="当前用户状态1:启用， 0：禁用， 2：异常"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="hierarchy",
	 *                  type="string",
	 *                  description="当前用户的等级"
	 *              ),
	 *				@SWG\Property(
	 *                  property="account_type",
	 *                  type="number",
	 *                  description="账号类型#1会员；2代理"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="link_type",
	 *                  type="number",
	 *                  description="链接类型#1站内；2外链"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="promote_url",
	 *                  type="number",
	 *                  description="推广链接"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="domains",
	 *                  type="string",
	 *                  description="网站后台所有可选择的链接"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="parents",
	 *                  type="string",
	 *                  description="用户层级"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="points",
	 *                  type="string",
	 *                  description="游戏返点数配置情况,这里返回的是数组对象[{id:这里其实是游戏的id,max_point:最大配置返点数,point:已经配置的返点数,name:游戏的名称}]"
	 *              )
	 *      )
	 *   )
	 * )
	 * **/
	public function details(Request $request,$self_id=0) {
		$name = $request->get('name');
		$id = $request->get('id');
		if($self_id>0){
			$id = $self_id;
		}
		
		if(empty($id) && empty($name)) {
			return response()->json(['code' => $this->errorStatus, 'error' => '请提交需要操作的会员'], $this->successStatus);
		}
		
		$PromoteConfig = new PromoteConfig();
		$CeilPoint = new CeilPoint();
		if($id > 0){
			$item = $PromoteConfig->find($id);
			if(empty($item) || $item->is_del == 1) {
				return response()->json(['code' => $this->errorStatus, 'error' => '没有找到推广链接项'], $this->successStatus);
			}
			$max_point = $CeilPoint->getPoint($item->uid,$item->ReturnPoint->toArray());
			
			$config = [
				'id'			=> $item->id,
				'status'		=> $item->user->UserInfo->getStatue(),
				'hierarchy'		=> $item->user->UserInfo->hierarchy,
				'name'			=> $item->user->name,
				'account_type'	=> $item->account_type,
				'link_type'		=> $item->link_type,
				'promote_url'	=> $item->promote_url,
				'points'		=> $max_point
			];
			
		}else{
			$item = $PromoteConfig;
			
			$Users = new Users();
			$user = $Users->getUserByName($name);
			
			if(empty($user)){
				return response()->json(['code' => $this->errorStatus, 'error' => '没有找到这个代理'], $this->successStatus);
			}
			
			$item->uid = $user->id;
			$max_point = $CeilPoint->getPoint($item->uid);
			
			$config = [
				'id'			=> 0,
				'status'		=> $user->Userinfo->getStatue(),
				'hierarchy'		=> $user->Userinfo->hierarchy,
				'name'			=> $user->name,
				'account_type'	=> 1,
				'link_type'		=> 1,
				'promote_url'	=> '',
				'points'		=> $max_point
			];
		}
		
		if(empty($max_point)) {
			return response()->json(['code' => $this->errorStatus, 'error' => '该用户不能添加返点链接'], $this->successStatus);
		}
		
		$UserInfos = new UserInfo();
		$parents = $UserInfos->getParentArr($item->uid);
		$config['parents'] = $parents;
		
		if($config['hierarchy'] > 5){
			return response()->json(['code' => $this->errorStatus, 'error' => '用户等级超过5级,不能再添加返点链接'], $this->successStatus);
		}
		
		$Domains = new Domain();
		$list = $Domains->select('id','name')->get()->toArray();
		$config['domains'] = $list;
		
		return response()->json(['code' => $this->successStatus, 'success' => $config], $this->successStatus);
	}
	
	/**
     * 新增、修改 推广链接
     *
     * @SWG\Post(
     *      path="/api/admPromote/save",
     *      tags={"admin-推广链接-huang"},
     *      summary="新增或者修改推广链接",
     *      description="新增或者修改推广链接，当提交ID的时候是修改，没有提交的时候是新增",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
	 *	   @SWG\Parameter(
     *          in="formData",
     *          name="id",
     *          type="integer",
     *          description="该条推广链接的id，可以提交也可以不提交",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="integer",
     *          description="对应用户的用户名",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="account_type",
     *          type="integer",
     *          description="账号类型，1代理；2会员",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="link_type",
     *          type="integer",
     *          description="链接类型,1站内；2外链",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="promote_url",
     *          type="integer",
     *          description="推广链接(本站地址)",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="points",
     *          type="string",
     *          description="返点配置（用json打包details里面获取的points的数据回来就好了）",
     *          required=true,
     *      ),
     *		@SWG\Response(
	 *          response="default",
	 *          description="返回该条链接的配置情况",
	 *          @SWG\Schema(
	 *              type="object",
	 *              @SWG\Property(
	 *                  property="id",
	 *                  type="number",
	 *                  description="该配置项的id"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="name",
	 *                  type="string",
	 *                  description="完整用户名"
	 *              ),
	 *				@SWG\Property(
	 *                  property="status",
	 *                  type="string",
	 *                  description="当前用户状态1:启用， 0：禁用"
	 *              ),
	 *				@SWG\Property(
	 *                  property="hierarchy",
	 *                  type="string",
	 *                  description="当前用户的等级"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="account_type",
	 *                  type="number",
	 *                  description="账号类型#1会员；2代理"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="link_type",
	 *                  type="number",
	 *                  description="链接类型#1站内；2外链"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="promote_url",
	 *                  type="number",
	 *                  description="推广链接"
	 *              ),
	 *				@SWG\Property(
	 *                  property="parents",
	 *                  type="string",
	 *                  description="用户层级"
	 *              ),
	 * 				@SWG\Property(
	 *                  property="points",
	 *                  type="string",
	 *                  description="游戏返点数配置情况,这里返回的是数组对象[{id:这里其实是游戏的id,max_point:最大配置返点数,point:已经配置的返点数,name:游戏的名称}]"
	 *              )
	 *			)
	 *		)
     *   )
     * )
     */
    public function save(Request $request) {
		$name = $request->get('name');
		$id = $request->get('id');
		
		$rule = [
			'id'			=> 'integer',
			'name'			=> 'string',
			'account_type'	=> 'required|integer',
			'link_type'		=> 'required|integer',
			'promote_url'	=> 'required|string',
			'points'		=> 'required|array',
		];
		
		$input = $request->all();
		try {
			$this->validator = Validator::make($input, $rule);
			$this->validatorFails();
		} catch (\Exception $e) {
			return response()->json(['code' => $this->errorStatus, 'error' => $e->getMessage()], $this->successStatus);
		}
		
		$PromoteConfig = new PromoteConfig();
		
		if(empty($id)){
			$Users = new Users();
			$user = $Users->getUserByName($name);
			if(empty($user)){
				return response()->json(['code' => $this->errorStatus, 'error' => '没有找到这个代理！'], $this->successStatus);
			}
			if($user->UserInfo->hierarchy > 5){
				return response()->json(['code' => $this->errorStatus, 'error' => '代理等级大于5,不能添加返点链接！'], $this->successStatus);
			}elseif($user->UserInfo->hierarchy == 5 && $input['account_type'] == 2){
				return response()->json(['code' => $this->errorStatus, 'error' => '代理为5,不能添加代理返点链接！'], $this->successStatus);
			}
			
			$promote = $PromoteConfig;
			$promote->uid = $user->id;
			$promote->addtime = time();
			$promote->updatetime = time();
			$promote->manage_id = CommonController::getUid();
			$promote->key = $promote->getOnlyKey($input['account_type']);
			
		}else{
			$promote = $PromoteConfig->find($id);
			if(empty($promote)){
				return response()->json(['code' => $this->errorStatus, 'error' => '没有找到这个配置信息！'], $this->successStatus);
			}
			if($promote->userinfo->hierarchy >= 5 && $input['account_type'] != 1){
				return response()->json(['code' => $this->errorStatus, 'error' => '代理的等级为5,不能再添加代理类型的链接！'], $this->successStatus);
			}
			$promote->updatetime = time();
		}
		
		$promote->account_type = $input['account_type'];
		$promote->link_type = $input['link_type'];
		$promote->promote_url = $input['promote_url'];
		
		//返点配置校验
		$back = $this->_checkMaxPoint($promote->uid, $input['points']);
		if(!$back){
			return $this->error(401, '返点数不能大于最大返点数,请重新设置!');
		}
		
		//入库操作
		$back = $promote->save();
		
		//处理return_point表里面的数据
		if($back){
//			$Returnpoint = new ReturnPoint(); //这里 不能这样定义，因为类的地址引用的，相当于是一个指针
			foreach ($input['points'] as $point){
				$Returnpoint = new ReturnPoint();  //每次都初始化一个类
				$item = $Returnpoint->where([
					['promote_id','=',$promote->id],
					['game_id','=',$point['game_id'] ],
					['uid','=',$promote->uid]
				])->first();
				
				if(empty($item)){
					$item = $Returnpoint;
					$item->addtime = time();
				}
				
				$item->promote_id = $promote->id;
				$item->game_id = $point['game_id'];
				$item->uid = $promote->uid;
				$item->point = $point['point'];
				
				$item->save();
			}
		}
		
		$CeilPoint = new CeilPoint();
		$max_point = $CeilPoint->getPoint($promote->uid,$promote->ReturnPoint->toArray());
		
		$config = [
			'id'			=> $promote->id,
			'status'		=> $promote->user->UserInfo->getStatue(),
			'hierarchy'		=> $promote->user->UserInfo->hierarchy,
			'name'			=> $promote->user->name,
			'account_type'	=> $promote->account_type,
			'link_type'		=> $promote->link_type,
			'promote_url'	=> $promote->promote_url,
			'points'		=> $max_point
		];
		$parents = $UserInfos->getParentArr($promote->uid);
		$config['parents'] = $parents;
		
		$Domains = new Domain();
		$list = $Domains->select('id','name')->get()->toArray();
		$config['domains'] = $list;
		
		return response()->json(['code' => $this->successStatus, 'success' => $config], $this->successStatus);
    }

    /**
     * 推广链接列表
     *
     * @SWG\Get(
     *      path="/api/admPromote/list",
     *      tags={"admin-推广链接-huang"},
     *      summary="推广链接列表",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
	 *		@SWG\Parameter(
     *          in="formData",
     *          name="account_type",
     *          type="string",
     *          description="账号类型#1会员；2代理",
     *          required=true,
     *      ),
	 *		@SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="账号",
     *          required=true,
     *      ),
	 *		@SWG\Parameter(
     *          in="formData",
     *          name="game_id",
     *          type="number",
     *          description="游戏ID 通过 /api/admConfig/getGame 可以获取到 如果不传或者传值0，不需要提交min和max这两个值，如果传了游戏id，需要提交min和max",
     *          required=true,
     *      ),
	 *		@SWG\Parameter(
     *          in="formData",
     *          name="min",
     *          type="number",
     *          description="最小返点数",
     *          required=true,
     *      ),
	 *		@SWG\Parameter(
     *          in="formData",
     *          name="max",
     *          type="number",
     *          description="最大返点数",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="一列数据",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @SWG\Property(
     *                      property="account_type",
     *                      type="string",
     *                      description="账号类型#1会员；2代理"
     *                  ),
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="所属代理"
     *                  ),
     *                  @SWG\Property(
     *                      property="pname",
     *                      type="string",
     *                      description="直属代理"
     *                  ),
     *                  @SWG\Property(
     *                      property="hierarchy",
     *                      type="number",
     *                      description="层级"
     *                  ),
     *                  @SWG\Property(
     *                      property="addtime",
     *                      type="string",
     *                      description="添加时间"
     *                  ),
     *                  @SWG\Property(
     *                      property="games",
     *                      type="arrays",
     *                      description="游戏返点"
     *                  )
     *              )
     *			)
     *		)
     *	)
     */
    public function getList(Request $request) {
		$name = $request->get('name');
		$account_type = $request->get('account_type');
		$game_id = $request->get('game_id');
		
		$promoteConfig = new PromoteConfig();
		$where = [];
		if($name){
			$where[] = ['name','like',"%{$name}%"];
		}
		if($account_type){
			$where[] = ['account_type','=',$account_type];
		}
		$where[] = ['is_del','=',2];
		if(intval($game_id) > 0){
			$min = $request->get('min');
			$max = $request->get('max');
			
			if(empty($max) || empty($min)) {
				$this->error('请提交查询范围');
			}
			
			$where['ex'] = [
				'game_id'	=> intval($game_id),
				'min'		=> $min,
				'max'		=> $max,
			];
		}
		
		$list = $promoteConfig->getList($where);
		
        return response()->json(['code' => $this->successStatus, 'success' => $list], $this->successStatus);
    }
	
    /**
     * 删除推广链接
     *
     * @SWG\post(
     *      path="/api/admPromote/del",
     *      tags={"admin-推广链接-huang"},
     *      summary="删除推广链接",
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
     *          description="数据id",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     *   )
     * )
     */
    public function del(Request $request) {
        $id = $request->get('id');
		
		$promoteConfig = new PromoteConfig();
		
		$link = $promoteConfig->find($id);
		
		if(empty($link)){
			return response()->json(['code' => $this->errorStatus, 'error' => '没有找到这个配置信息！'], $this->successStatus);
		}
		
		$link->is_del=1;
		$link->updatetime=time();
		
		$back = $link->save();
		
		$link = $link->toArray();
		
        return response()->json(['code' => $this->successStatus, 'success' => $link ], $this->successStatus);
    }

    /**
     * 返点上限校验
     * */
    private function _checkMaxPoint($uid, $point) {
        if(empty($point) || is_array($point) == false) {
            return false;
        }
		$Userinfo = new UserInfo();
		$user = $Userinfo->find($uid);
		//把用户找出来，如果不存在就不能设置
		if(!$user){
			return false;
		}
		$CeilPoint = new CeilPoint();
		$max_points = $CeilPoint->getPoint($uid);

        foreach($point as $k => $v) {
			if($v['point'] > $max_points[$v['game_id']]['max_point']){
				return false;
			}
        }
		return true;
    }
}