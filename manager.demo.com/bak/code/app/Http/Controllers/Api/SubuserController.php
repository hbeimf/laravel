<?php
/**
 * 下级用户管理
 */
namespace App\Http\Controllers\Api;

use App\Http\Model\PromoteConfig;
use App\Http\Model\UserInfo;
use App\Http\Model\Game;
use App\Http\Model\Domain;
use App\Http\Model\CeilPoint;
use App\Http\Model\ReturnPoint;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use \Logger; 


class SubuserController extends CommonController
{
	private $user;

	protected function getUser() {
		return Auth::user();
	}
	
    /**
	 * 获取游戏列表
	 *
	 * @SWG\Get(
	 *      path="/api/h5/getGameList",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】获取游戏列表",
	 *      operationId="gameList",
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
	 *					property="hierarchy",
	 *					type="string", 
	 *					description="代理等级",
	 *				),
	 *				@SWG\Property(
	 *				property="gameList",	
	 *				type="array",
	 *              @SWG\Items(
	 *						@SWG\Property(
	 *							property="id",
	 *							type="string", 
	 *							description="游戏id",
	 *						),
	 *						@SWG\Property(
	 *							property="name",
	 *							type="string", 
	 *							description="游戏名称",
	 *						),
	 *						@SWG\Property(
	 *							property="note",
	 *							type="string", 
	 *							description="备注"
	 *						),
	 *						@SWG\Property(
	 *							property="maxPoint",
	 *							type="string", 
	 *							description="上级限制你的最大返点数"
	 *						),
	 *						@SWG\Property(
	 *							property="inputPoint",
	 *							type="string", 
	 *							description="空字符串"
	 *						),
	 *					),
	 *				)
	 *      )
	 *   )
	 * )
	 */
	public function getGameList() {
		$gameModel = new Game();
		$userModel = new UserInfo();
		$ceilPointModel = new CeilPoint();
		$returnPointModel = new ReturnPoint();
		$this->user = $this->getUser();
		
		$gameList = $gameModel->where('status', 1)->get()->toArray();
		$userInfo = $userModel->where('uid', $this->user->id)->first();
		$ceilPoint = $ceilPointModel->where('uid', $this->user->id)->get(); // 上级代理给下级代理最大返点限制
		$returnPoint = $returnPointModel->where('promote_id', $userInfo->promote_id)->get(); // 上级返点数

		foreach ($gameList as &$val) {
			unset($val['status']);
			$val['point'] = '0';
			$val['maxPoint'] = $val['max_point'];
			$val['inputPoint'] = "";

			if (count($ceilPoint) > 0) {
				foreach($ceilPoint as $ceilV) {
					if ($ceilV->game_id == $val['id']) {
						$val['maxPoint'] = isset($ceilV->max_point) ? $ceilV->max_point : $val['max_point'];
						break;
					}
				}
			}
			if ($userInfo->hierarchy == 1) {
				$returnPoint = $returnPointModel->where([['uid', $this->user->id], ['promote_id', 0]])->get(); // 上级返点数
			} 
			foreach ($returnPoint as $rp) {
				if ($rp->game_id == $val['id']) {
					$val['point'] = $rp->point;
					$val['maxPoint'] = ($val['maxPoint'] > $val['point']) ? $val['point'] : $val['maxPoint'];
				}
			}
			
			unset($val['max_point']);
		}
		
		return response()->json(['data' => ['hierarchy' => $userInfo->hierarchy, 'gameList' => $gameList], 'code' => 200]);
	}
	
	
    /**
	 * 下级开户
	 *
	 * @SWG\Post(
	 *      path="/api/h5/addGameUrl",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】添加推广链接",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="game",
     *          type="string",
     *          description="游戏列表json串如{'gameId':1,'point':'5'}，gameId游戏id，point返点数",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="accountType",
     *          type="integer",
     *          description="账号类型1会员，2代理，3试玩",
     *          required=true,
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="添加成功",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *						@SWG\Property(
	 *							property="domain",
	 *							type="string", 
	 *							description="推广链接域名",
	 *						),
	 *						@SWG\Property(
	 *							property="key",
	 *							type="string", 
	 *							description="邀请码key",
	 *						),
	 *						@SWG\Property(
	 *							property="url",
	 *							type="string", 
	 *							description="推广链接",
	 *						),
	 *              )
	 *			)
	 *		)
	 *	)
	 */
	public function addGameUrl(Request $request) {
		$gameList = $request->game;
		$accountType = $request->accountType;
		
		try{
			$gameList = json_decode($gameList, true);
		} catch(\Exception $e) {
			$msg = $e->getMessage();
			return response()->json(['msg' => $msg, 'code' => 401]);
		}
		
		$this->user = $this->getUser();
		$pConfigModel = new PromoteConfig();
		$domainModel = new Domain();
		$userModel = new UserInfo();
		$returnPonitModel = new ReturnPoint();
		$ceilPointModel = new CeilPoint();
		$gameModel = new Game();
		$now = time();
		$domain = $domainModel->where('is_default', 1)->first()->toArray(); // 推广链接
		$url = $domain['name'];
		
		$userInfo = $userModel->where('uid', $this->user->id)->first();
		if ($userInfo->user_type != 2) {
			return response()->json(['msg' => '抱歉，你不是代理用户', 'code' => 401]);
		}
		if ($userInfo->hierarchy > 5) {
			return response()->json(['msg' => '抱歉，你不能生成代理链接', 'code' => 401]);
		}
		
		$key = $pConfigModel->getOnlyKey($accountType);// 邀请码
		
		DB::beginTransaction();
		try{
			$obj = $pConfigModel->create([
				'uid' => $this->user->id,
				'account_type' => $accountType,
				'promote_url' => $url,
				'addtime' => $now,
				'updatetime' => $now,
				'key' => $key
			]);
			if ( ! isset($obj->id)) {
				DB::rollback();
				return response()->json(['msg' => '创建下级异常', 'code' => 401]);
			}

			// 判断上级返点
			$parentRealPoint = $returnPonitModel->where('promote_id', $userInfo->promote_id)->get(); // 上级实际下发的返点数
			$ceilPoint = $ceilPointModel->where('uid', $this->user->id)->get(); // 上级代理给下级代理最大返点限制
			$userInfo->hierarchy == 1 && $parentRealPoint = $returnPonitModel->where([['uid', $this->user->id], ['promote_id', 0]])->get();
			if ($this->checkMaxPoint($gameList, $ceilPoint, $parentRealPoint, $userInfo->hierarchy) < 0) {
				DB::rollback();
				return response()->json(['msg' => '返点数有误', 'code' => 401]);
			}

			foreach ($gameList as $val) {
				if (! isset($val['gameId']) || ! isset($val['point'])) {
					DB::rollback();
					return response()->json(['msg' => '参数有误', 'code' => 401]);
				}
				$exist = $returnPonitModel->where(['promote_id' => $obj->id, 'game_id' => $val['gameId'], 'uid' => $this->user->id])->first();
				if (isset($exist->game_id)) { // 推广链接已经存在对应的记录
					continue;
				}
				$returnPonitModel->create([
					'uid' => $this->user->id,
					'promote_id' => $obj->id,
					'game_id' => $val['gameId'],
					'point' => $val['point'],
					'addtime' => $now,
					'update_at' => date('Y-m-d H:i:s')
				]);
			}
			DB::commit();
		} catch(\Exception $e) {
			DB::rollback();//事务回滚
			$msg = $e->getMessage();
			$code = $e->getCode();
			return response()->json(['data' => false, 'code' => $code, 'msg' => $msg]);
		}
		
		$ret = [
			'domain' => $url,
			'key' => $key,
			'promoteId' => $obj->id,
		];
		$this->log->DEBUG("用户ID " . $this->user->id . " 增加推广链接ID " . $obj->id);
		
		return response()->json(['data' => $ret, 'code' => 200]);
	}
	
	/**
	 * 邀请码管理列表
	 *
	 * @SWG\Get(
	 *      path="/api/h5/urlList",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】用户的所有推广链接列表",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="accountType",
     *          type="string",
     *          description="账号类型1会员，2代理",
     *          required=true,
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *						@SWG\Property(
	 *							property="pid",
	 *							type="string", 
	 *							description="推广链接的id，增删改查对应返回给后端的id",
	 *						),
	 *						@SWG\Property(
	 *							property="domain",
	 *							type="string", 
	 *							description="推广链接域名",
	 *						),
	 *						@SWG\Property(
	 *							property="key",
	 *							type="string", 
	 *							description="邀请码",
	 *						),
	 *						@SWG\Property(
	 *							property="registerNum",
	 *							type="integer", 
	 *							description="推广链接对应的注册人数",
	 *						),
	 *						@SWG\Property(
	 *							property="createAt",
	 *							type="string", 
	 *							description="添加时间",
	 *						),
	 *              )
	 *			)
	 *		)
	 *	)
	 */
	public function urlList(Request $request) {
		$accountType = isset($request->accountType) ? $request->accountType : 2;
		$pConfigModel = new PromoteConfig();
		$this->user = $this->getUser();
		$pConfig = $pConfigModel->where([['uid', $this->user->id], ['is_del', '2'], ['account_type',$accountType]])->orderBy('id', 'desc')->get()->toArray(); // 推广链接列表
		$ret = $tmp = [];
		
		foreach ($pConfig as $val) {
			$tmp['pid'] = $val['id']; // 推广链接id
			$tmp['domain'] = $val['promote_url'];
			$tmp['key'] = $val['key'];
			$tmp['registerNum'] = $val['register_num'];
			$tmp['createAt'] = date('Y/m/d', $val['addtime']);
			if ( ! preg_match('/^http:\/\//', $tmp['domain'])) {
				$tmp['domain'] = "http://".$tmp['domain'];
			}
			$ret[] = $tmp;
		}
		return response()->json(['data' => $ret, 'code' => 200]);
	}

    /**
     * 获取默认邀请码
     *
     * @SWG\Get(
     *      path="/api/getDefaultUrl",
     *      tags={"api-h5前端-wyg"},
     *      summary="获取默认邀请码",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="key",
     *                  type="string",
     *                  description="推广链接key"
     *              ),
     *
     *      )
     *   )
     * )
     */
	public function getDefaultUrl() {
	    $host = $_SERVER["HTTP_HOST"];
	    $model = new PromoteConfig();
	    $this->result['data'] = $model->getDefaultKey($host, ['key']);
        return response()->json($this->result);
    }
	
	
	/**
	 * 修改返点数
	 *
	 * @SWG\Get(
	 *      path="/api/h5/editUrlList",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】单个推广链接下的游戏返点数列表",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="pid",
     *          type="integer",
	 *			required=true,
     *          description="返点链接id",
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="获取成功",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *					@SWG\Property(
	 *						property="gameName",
	 *						type="gameId", 
	 *						description="游戏名称",
	 *					),
	 *					@SWG\Property(
	 *						property="gameId",
	 *						type="integer", 
	 *						description="游戏id",
	 *					),
	 *					@SWG\Property(
	 *						property="point",
	 *						type="string", 
	 *						description="返点数，null为该游戏还从未设置过返点数",
	 *					),
	 *					@SWG\Property(
	 *						property="maxPoint",
	 *						type="string", 
	 *						description="自身返点【上级限制的下级的返点数】",
	 *					)
	 *              )
	 *			)
	 *		)
	 *	)
	 */
	public function editUrlList(Request $request) {
		$pid = $request->pid;
		if (empty($pid)) {
			return response()->json(['msg' => '参数错误', 'code' => 401]);
		}
		$this->user = $this->getUser();
		
		$pConfigModel = new promoteConfig();
		$returnPonitModel = new ReturnPoint();
		$ceilPointModel = new CeilPoint();
		$userModel = new UserInfo();
		$gameModel = new game();
		$exist = $pConfigModel->where([['uid', $this->user->id], ['id', $pid], ['is_del', '2']])->first();
		if ( ! isset($exist->id)) {
			return response()->json(['msg' => '推广链接异常', 'code' => 401]);
		}
		
		$gameList = $gameModel->where('status', 1)->get()->toArray();
		$pointList = $returnPonitModel->where('promote_id', $pid)->get();
		$userInfo = $userModel->where('uid', $this->user->id)->first();
		$ceilPoint = $ceilPointModel->where('uid', $this->user->id)->get(); // 上级代理给下级代理最大返点限制
		$parentPoint = $returnPonitModel->where('promote_id', $userInfo->promote_id)->get(); // 上级实际下发给我的返点
		
		$ret = [];
		foreach ($gameList as &$val) {
			$tmp = [
				'gameName' => $val['name'],
				'gameId' => $val['id'],
				'point' => '0',
				'maxPoint' => $val['max_point'], // 默认最大返点10
			];

			if (count($pointList) > 0) {
				foreach($pointList as $v) {
					if ($val['id'] == $v->game_id) {
						$tmp['point'] = $v->point;
					}
				}
			}
			if (count($ceilPoint) > 0) { // 上级在后台设置的限制下级返点
				foreach ($ceilPoint as $vv) {
					if ($vv->game_id == $val['id']) {
						$tmp['maxPoint'] = $vv->max_point;
					}
				}
			}
			if (count($parentPoint) > 0) { // 上级实际返回给我的返点
				foreach ($parentPoint as $vp) {
					if ($val['id'] == $vp->game_id) {
						$tmp['maxPoint'] = $vp->point < $tmp['maxPoint'] ? $vp->point : $tmp['maxPoint'];
					}
				}
			}
			$ret[] = $tmp;
		}
		
		return response()->json(['data' => $ret, 'code' => 200]);
	}
	
	/**
	 * 保存修改的返点列表
	 *
	 * @SWG\Post(
	 *      path="/api/h5/saveUrlEdit",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】保存修改的返点列表",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="pid",
     *          type="integer",
	 *			required=true,
     *          description="返点链接的id",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="gameData",
     *          type="integer",
	 *			required=true,
     *          description="游戏列表json串如{'gameId':1,'point':'5'}，gameId游戏id，point返点数",
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="修改成功",
	 *		)
	 *	)
	 */
	public function saveUrlEdit(Request $request) {
		$pid = $request->pid;
		$game = $request->gameData;
		$game = json_decode($game, true);
		$this->user = $this->getUser();
		if (empty($pid) || empty($game) || ! is_array($game)) {
			return response()->json(['msg' => '参数错误', 'code' => 401]);
		}
	
		$returnPonitModel = new ReturnPoint();
		$userModel = new UserInfo();
		$ceilPointModel = new CeilPoint();
		$gameModel = new Game();
		$userInfo = $userModel->where('uid', $this->user->id)->first();
		$parentData = $returnPonitModel->where('promote_id', $userInfo->promote_id)->get(); // 上级给的返点推广链接
		$ceilPoint = $ceilPointModel->where('uid', $this->user->id)->get(); // 上级代理给下级代理最大返点限制
		$existData = $returnPonitModel->where('promote_id', $pid)->get(); // 当前修改的推广链接
		$userInfo->hierarchy == 1 && $parentData = $returnPonitModel->where([['uid', $this->user->id], ['promote_id', 0]])->get();

		if ($userInfo->user_type != 2) {
			return response()->json(['msg' => '抱歉，你不是代理用户', 'code' => 401]);
		}
		if ($userInfo->hierarchy > 5) {
			return response()->json(['msg' => '抱歉，你不能生成代理链接', 'code' => 401]);
		}
		// 判断上级返
		if ($this->checkMaxPoint($game, $ceilPoint, $parentData, $userInfo->hierarchy) < 0) {
			return response()->json(['msg' => '返点数有误', 'code' => 401]);
		}
		
		DB::beginTransaction();
		try{
			foreach($game as $val){
				if ( ! isset($val['gameId']) || !isset($val['point'])) { // 参数不对
					continue;
				}
				$getGame = false;
				foreach ($existData as $v) {
					if ($v->game_id == $val['gameId']) {
						$getGame = true;
					}
				}
				if ($getGame) { // 现有数据，只是修改
					$returnPonitModel->where([['game_id', $val['gameId']],['promote_id', $pid]])
						->update(['point' => $val['point'], 'update_at' => date('Y-m-d H:i:s')]);
				} else { // 找不到对应记录，新增
					$returnPonitModel->create([
						'uid' => $this->user->id,
						'promote_id' => $pid,
						'game_id' => $val['gameId'],
						'point' => $val['point'],
						'addtime' => time(),
						'updatetime' => time()
					]);
				}
			}
			DB::commit();
		} catch(\Exception $e) {
			DB::rollback();//事务回滚
			$msg = $e->getMessage();
			$code = $e->getCode();
			return response()->json(['data' => false, 'code' => $code, 'msg' => $msg]);
		}
		
		$this->log->DEBUG("用户ID " . $this->user->id . " 修改推广链接ID " . $pid);
		
		return response()->json(['data' => true, 'code' => 200]);
	}
	
	
	/**
	 * 检测是否修改点数小于上级限制
	 * @param type $data // 修改的point
	 * @param type $maxData // 上级返点限制point
	 * @param type $realData // 上级给的返点数
	 * @param type $hierarchy // 代理等级
	 * @return boolean
	 */
	private function checkMaxPoint($data, $maxData, $realData, $hierarchy=1) {
		$canChangePoint = [];

		foreach ($realData as $rd) {
			$canChangePoint[$rd->game_id] = $rd->point;
			if (count($maxData) == 0) {
				continue;
			}
			foreach ($maxData as $md) {
				if ($md->game_id == $rd->game_id) {
					if ($md->max_point < $rd->point) { // 有设置最大值
						$canChangePoint[$rd->game_id] = $md->max_point;
					}
				}
			}
		}

		foreach ($data as $d) {
			if ( ! isset($canChangePoint[$d['gameId']])) { // 上级对该游戏未设置返点
				return -1;
			}
			if ($d['point'] > $canChangePoint[$d['gameId']]) { // 最大返点数小于被设置数
				return -2;
			}
			if ($d['point'] < 0) {
				return -3;
			}
			if ( ! is_numeric($d['point'])) {
				return -4;
			}
		}

		return 1;
	}
	
	/**
	 * 删除下级注册链接
	 * 
	 * @SWG\Get(
	 *      path="/api/h5/delUrl",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】删除推广链接",
	 *      operationId="addUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="pid",
     *          type="integer",
	 *			required=true,
     *          description="要删除的返点链接id",
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="删除异常",
	 *		),
	 *      @SWG\Response(
	 *          response="200",
	 *          description="删除成功",
	 *		)
	 *	)
	 */
	public function delUrl(Request $request) {
		$pid = $request->pid;
		if (empty($pid)) {
			return response()->json(['msg' => '参数错误', 'code' => 401]);
		}
		$this->user = $this->getUser();
		$pConfigModel = new promoteConfig();
		
		$exist = $pConfigModel->selectRaw('id')->where([['id', $pid], ['uid', $this->user->id]])->first();
		if ( ! isset($exist->id)) {
			return response()->json(['msg' => '删除异常', 'code' => 401]);
		}
		$pConfigModel->where('id', $pid)->update(['is_del' => 1]);
		return response()->json(['data' => true, 'code' => 200]);
	}
	
	
	/**
	 * 获取下级用列表
	 *
	 * @SWG\Post(
	 *      path="/api/h5/getSubser",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="【返点】获取下级用列表",
	 *      operationId="getSubser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="page",
     *          type="integer",
	 *			required=true,
     *          description="当前页，默认1",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="size",
     *          type="integer",
     *          description="每页显示几条，默认10条",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="integer",
     *          description="用户名，模糊查询",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="accoutType",
     *          type="integer",
     *          description="用户类型，1：会员， 2:代理， 3:试玩， 0所有用户",
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
	 *					property="total",
	 *					type="integer", 
	 *					description="数据总条数",
	 *				),
	 *				@SWG\Property(	
	 *					property="lastPage",
	 *					type="integer", 
	 *					description="总页数",
	 *				),
	 *				@SWG\Property(	
	 *					property="currentPage",
	 *					type="integer", 
	 *					description="当前页",
	 *				),
	 *				@SWG\Property(
	 *				property="gameList",	
	 *				type="array",
	 *              @SWG\Items(
	 *						@SWG\Property(
	 *							property="name",
	 *							type="string", 
	 *							description="账户名",
	 *						),
	 *						@SWG\Property(
	 *							property="accountType",
	 *							type="string", 
	 *							description="用户类型，1：会员， 2:代理， 3:试玩",
	 *						),
	 *						@SWG\Property(
	 *							property="registerNum",
	 *							type="integer", 
	 *							description="注册用户数"
	 *						),
	 *						@SWG\Property(
	 *							property="money",
	 *							type="string", 
	 *							description="账户余额"
	 *						),
	 *						@SWG\Property(
	 *							property="createdAt",
	 *							type="string", 
	 *							description="创建时间"
	 *						)
	 *					),
	 *				)
	 *			)
	 *		)
	 *	)
	 */
	public function getSubser (Request $request) {
		$accountType = $request->accoutType;
		$name = $request->name;
		$size = isset($request->size) ? $request->size : 10;
		$userModel = new UserInfo();
		$this->user = $this->getUser();
		$where = [['bw_user_info.parent_uid', $this->user->id],['bw_user_info.user_type', '<>', 3]];
		if (isset($accountType) && $accountType != 0) {
			$where[] = ['bw_user_info.user_type', $accountType];
		}
		if (isset($name)) {
			$where[] = ["u.name", "like", "%".$name."%"];
		}
		$pageInfo = $userModel->getUserByParentUid($where, $size);
		$ret = [];
		foreach ($pageInfo['data'] as $val) {
			$ret[] = [
				'name' => $val['name'],
				'accountType' => $val['user_type'],
				'registerNum' => $userModel->where('parent_uid', $val['uid'])->count(),
				'money' => isset($val['total_money']) ? $val['total_money'] : 0,
				'createdAt' => $val['create_at']
			];
		}

		$data = [
			'list' => $ret,
			'total' => $pageInfo['total'],
			'lastPage' => $pageInfo['last_page'],
			'currentPage' => $pageInfo['current_page'],
		];
		
		return response()->json(['data' => $data, 'code' => 200]);
	}

}