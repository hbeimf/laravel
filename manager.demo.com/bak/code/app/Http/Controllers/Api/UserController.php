<?php
// https://www.jianshu.com/p/2988ba405b3b?from=timeline&isappinstalled=0


namespace App\Http\Controllers\Api;

use App\Helper\Fn;
use App\Http\Model\PromoteConfig;
use App\Http\Model\UserInfo;
use App\Http\Model\UserRelevantInfo;
use extend\captcha\VCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;
use App\User;
use App\Http\Model\Account;
use App\Http\Model\File;
use App\Http\Model\Users;
use App\Http\Model\LevelConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;


class UserController extends CommonController
{

    /**
     * 登录
     *
     * @SWG\Post(
     *      path="/api/login",
     *      tags={"api-user"},
     *      summary="登录",
     *      @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="用户名",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="登录token",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string",
     *                  description="token"
     *              ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="出错了",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="num",
     *                  type="integer",
     *                  description="连续登录错误次数"
     *              ),
     *          )
     *      ),
     * )
     */
    public function login(Request $request) {
        try{
            $this->log->debug("登录");
            $rule = [
                'name'  => 'required',
                'password'  => 'required',
            ];

            $this->validator = Validator::make($request->all(), $rule);
            $this->validatorFails();

            $name = request('name');
            if(Auth::attempt(['name' => $name, 'password' => request('password')]) == false) {
                $uid = UserInfo::where('mobile', request('name'))->value('uid');

                if(!$uid) {
                    $uid = User::where('name', request('name'))->value('id');
                }
            } else {
                $uid = Auth::id();
            }

            $this->_checkFailNum($name);
            $this->_isCanLogin($name, $uid);

            if( ($uid && Auth::attempt(['id' => $uid, 'password' => request('password')])) ) {
                $user = Auth::user();
                $success['token'] =  $user->createToken('MyApp')->accessToken;
                //记录登录时间
                $data['login_time'] = time();
                $data = [
                    'login_num'     => DB::raw('`login_num`+1'),
                    'login_time'     => time(),
                ];
                UserInfo::where('uid', $user->id)->update($data);

                //登录失败缓存清空
                $loginCacheKey = config('cache-key.LOGIN_FAIL_NUM').$name;
                if(cache()->has($loginCacheKey)) {
                    cache()->forget($loginCacheKey);
                }

                $this->result['data'] = $success;
            } else {
                //登录失败连续5次 冻结账号
                $this->_frost($name, $uid);
                throw new \Exception('用户账号或登录密码错误', 4002);
            }

        } catch (\Exception $e) {
            $num = cache(config('cache-key.LOGIN_FAIL_NUM').request('name'));
            $this->result = [
                'code'  => $e->getCode(),
                'msg'  => $e->getMessage(),
                'data'  => ['num' => $num],
            ];
        }
        return response()->json($this->result);
    }

    /**
     *失败4次验证验证码
     * */
    private function _checkFailNum($name) {
        if(cache(config('cache-key.LOGIN_FAIL_NUM').$name) >= 4) {
            $rule = [
                'captcha' => 'required|captcha:'.request('captcha_key'),
            ];
            $msg = [
                'captcha.captcha'   => '验证码错误',
            ];
            $this->validator = Validator::make(request()->all(), $rule, $msg);
            $this->validatorFails();

        }
    }

    /**
     * 失败五次冻结账号
     * */
    private function _frost($name, $uid) {
        //登录失败连续5次 冻结账号
        $loginCacheKey = config('cache-key.LOGIN_FAIL_NUM').$name;

        if(cache()->has($loginCacheKey) == false) {
            cache([$loginCacheKey => 0], 60*24);
        }
        cache()->increment($loginCacheKey);
        if(cache($loginCacheKey) > 5 && $uid) {
            UserInfo::where('uid', $uid)->update(['status3' => UserInfo::STATUS3_NO]);
        }
    }

    /**
     * 判断用户是否可以登录
     * */
    private function _isCanLogin($name, $uid) {
        if(empty($uid)) {
            $this->_frost($name, $uid);
            throw new \Exception('用户账号或登录密码错误', 4008);
        }
        $userInfo = UserInfo::where('uid', $uid)->first(['status1', 'status3', 'status4']);
        if($userInfo->status1 != 1 || $userInfo->status3 != 1) {
            throw new \Exception('该账号已冻结,请联系客服人员', 4007);
        }
    }

    /**
     * 注册
     *
     * @SWG\Post(
     *      path="/api/register",
     *      tags={"api-user"},
     *      summary="注册",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="formData",
     *          name="mode",
     *          type="integer",
     *          description="注册方式#1:手机注册；2：普通注册",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="mobile|name",
     *          type="string",
     *          description="用户名#手机注册字段mobile；普通注册字段name",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="密码",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="c_password",
     *          type="string",
     *          description="确认密码",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="captcha",
     *          type="string",
     *          description="验证码",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="captcha_key",
     *          type="string",
     *          description="验证码key-验证码接口返回",
     *          required=true,
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="register_url",
     *          type="string",
     *          description="注册链接",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string",
     *                  description="token"
     *              ),
     *
     *      )
     *   )
     * )
     */
    public function register(Request $request)
    {
        try{
            $this->log->debug("注册！！");
            $rule = [
                'password' => 'required|min:6|alpha_dash_must',
                'c_password' => 'required|same:password',
                'captcha' => 'required|captcha:'.$request->captcha_key,
            ];
            $msg = [
                'captcha.captcha'   => '验证码错误',
                'password.min'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
                'password.alpha_dash_must'      => '密码格式不正确,至少6位字符，可包含数字,字母,下划线',
            ];
            $input = $request->all();
            if(request('mode', 1) == 1) {
                $rule['mobile'] = 'required|phone|unique:bw_user_info';
                $userInfoData = [
                    'mobile'   => $input['mobile'],
                ];

            } else {
                $rule['name'] = 'required|between:6,20|alpha_dash_chinese|unique:users';
                $userData = [
                    'name'  => $input['name'],
                ];
                $msg['name.required'] = '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文';
                $msg['name.between'] = '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文';
                $msg['name.alpha_dash_chinese'] = '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文';
                $msg['name.unique'] = '该账号已存在';
            }
            $this->validator = Validator::make($input, $rule, $msg);
            $this->validatorFails();

            $userInfoData['register_ip'] = $request->getClientIp();
            $register_url = isset($input['register_url']) ? $input['register_url'] : '';
            $userInfoData['register_url'] = $register_url;
            //处理注册链接
            $parentInfo = $this->_dealInvite($register_url);

            $promoteId = 0;
            if($parentInfo) {
                $promoteId = $parentInfo['promote_id'];
                $userInfoData['parent_uid'] = $parentInfo['uid'];
                $userInfoData['promote_id'] = $parentInfo['promote_id'];
                $userInfoData['parent_uid_dir'] = $parentInfo['parent_uid_dir']. $parentInfo['uid']. ';';
                $userInfoData['hierarchy'] = $parentInfo['hierarchy'] + 1;
                $userInfoData['user_type'] = $userInfoData['hierarchy'] > 5 ? UserInfo::USER_TYPE_NORMAL : $parentInfo['account_type'];
            }
            $userData['password'] = bcrypt($input['password']);
            //入库操作
            DB::transaction(function ()use($userData, $userInfoData, &$user, $promoteId)
            {
                $user = User::create($userData);
                $userInfoData['uid'] = $user->id;

                UserInfo::insert($userInfoData);

                //推广链接下+1
                PromoteConfig::where('id', $promoteId)->increment('register_num');
            });

            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $this->result['data'] = $success;
        } catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 用户详情
     *
     * @SWG\Get(
     *      path="/api/details",
     *      tags={"api-user"},
     *      summary="用户详情",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer",
     *                  description="id"
     *              ),
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  description="用户名"
     *              ),
     *              @SWG\Property(
     *                  property="avatar",
     *                  type="string",
     *                  description="头像"
     *              ),
     *              @SWG\Property(
     *                  property="balance",
     *                  type="number",
     *                  description="余额"
     *              ),
     *
     *      )
     *   )
     * )
     */
    public function details()
    {
        $this->log->debug("检查oauth2.0登录权限，再查看详情");
        $user = Auth::user();
        $user->name = empty($user->name) ? $user->mobile : $user->name;
        $account = new Account();
        $result = $account->getAccountByUid($user->id, ['balance', 'amount_frozen']);
        $user->balance = isset($result->balance) ? $result->balance : '0.00';
        $this->result['data'] = $user;
        return response()->json($this->result, $this->successStatus);
    }

    /**
     * 退出登录
     *
     * @SWG\Get(
     *      path="/api/loginOut",
     *      tags={"api-user"},
     *      summary="退出登录",
     *      description="请求该接口需要先登录。",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     * )
     */
    public function loginOut() {
        $this->log->debug("退出登录");
        Auth::user()->token()->revoke();
        return response()->json($this->result);
    }

    /**
     * 试玩账号注册
     *
     * @SWG\Post(
     *      path="/api/guestRegister",
     *      tags={"api-user"},
     *      summary="试玩账号注册",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="default",
     *          description="token",
     *          @SWG\Schema(
     *              type="string",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string",
     *                  description="token"
     *              ),
     *
     *      )
     *   )
     * )
     */
    public function guestRegister(Request $request) {
        try{
            $this->log->debug("试玩注册！！");
            $data = [
                'name'  => 'guest'.\App\Helper\Fn::getRandStr(7, 3),
                'password' => bcrypt('123456'),
                'register_ip' => $request->getClientIp(),
                'user_type' => UserInfo::USER_TYPE_TRY,
                'login_time' => time(),
            ];
            $rule = [
                'name'  => 'required|between:6,20|unique:users',
            ];
            $this->validator = Validator::make($data, $rule);
            $this->validatorFails();

            //入库操作
            DB::transaction(function ()use($data, &$user)
            {
                $user = User::create(['name' => $data['name'], 'password' => $data['password']]);
                $userInfoData = [
                    'uid'   => $user->id,
                    'register_ip'   => $data['register_ip'],
                    'user_type'   => $data['user_type'],
                    'login_time'   => $data['login_time'],
                ];

                UserInfo::insert($userInfoData);
            });

            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $this->result['data'] = $success;
        } catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 修改登录密码
     *
     * @SWG\Post(
     *      path="/api/changePassword",
     *      tags={"api-user"},
     *      summary="修改登录密码",
     *      produces={"application/json"},
     *     security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="oldpassword",
     *          type="string",
     *          description="原密码",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="password",
     *          type="string",
     *          description="密码",
     *          required=true,
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="c_password",
     *          type="string",
     *          description="确认密码",
     *          required=true,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="操作成功",
     *      )
     *   )
     * )
     */
    public function changePassword(Request $request) {
        try{
            $this->log->debug("重置密码！！");
            $rule = [
                'oldpassword' => 'required|min:6',
                'password' => 'required|min:6|alpha_dash_must',
                'c_password'    =>'required|same:password',
            ];
            $input = $request->all(); //接收所有的数据
            $validator = Validator::make($input, $rule);

            $user = Auth::user();
            $oldpassword = $input['oldpassword'];
            $validator->after(function($validator) use ($oldpassword, $user) {
                if (!\Hash::check($oldpassword, $user->password)) { //原始密码和数据库里的密码进行比对
                    $validator->errors()->add('oldpassword', "原密码错误");
                }
            });
            $this->validator = $validator;
            $this->validatorFails();

            $user->password = bcrypt($input['password']);
            $user->save();
//            Auth::logout();  //更改完这次密码后，退出这个用户
        } catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
        }
        return response()->json($this->result);
    }

    /**
     * 获取验证码
     *
     * @SWG\Get(
     *      path="/api/getCaptcha",
     *      tags={"api-common"},
     *      summary="获取验证码",
     *     @SWG\Parameter(
     *          in="query",
     *          name="color",
     *          type="string",
     *          description="字体颜色",
     *          required=false,
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="bgColor",
     *          type="string",
     *          description="背景颜色",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response="default",
     *          description="图片数组",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *
     *                  @SWG\Property(
     *                      property="key",
     *                      type="string",
     *                      description="key"
     *                  ),
     *                  @SWG\Property(
     *                      property="img",
     *                      type="string",
     *                      description="img"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function getCaptcha() {
        $width = (int)request('width');
        $height = (int)request('height');
        $color = request('color', '');
        $bgColor = request('bgColor', '');
        $vcode = new VCode();
        $vcode->_colorf = $color;
        $vcode->bg = $bgColor;
        $key = md5(time().Fn::getRandStr(8));
        cache([$key => strtolower($vcode->getCode())], 5);
        $this->result['data']['key'] = $key;
        $this->result['data']['img'] = $vcode->outImg();
        return response()->json($this->result);


    }

    /**
	 * 获取用户信息
	 *
	 * @SWG\Get(
	 *      path="/api/userinfo",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="获取前端用户信息",
	 *      operationId="userinfo",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了"
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="用户信息",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 *                      property="picture",
	 *                      type="string",
	 *                      description="头像"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="nickname",
	 *                      type="string",
	 *                      description="昵称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="integer",
	 *                      description="用户id"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="sex",
	 *                      type="integer",
	 *                      description="0未知1男2女"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="birthday",
	 *                      type="string",
	 *                      description="生日"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="wechat",
	 *                      type="string",
	 *                      description="微信账号"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="level",
	 *                      type="string",
	 *                      description="用户等级名称"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="mobile",
	 *                      type="string",
	 *                      description="手机号"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="money",
	 *                      type="string",
	 *                      description="账户余额"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="usertype",
	 *                      type="string",
	 *                      description="用户类型，1：会员， 2:代理， 3:试玩"
	 *                  ),
	 *                  @SWG\Property(
	 *                      property="picLevel",
	 *                      type="string",
	 *                      description="玩家等级图片"
	 *                  ),
	 *              )
	 *      )
	 *   )
	 * )
	 */
	public function getUserInfo() {
		$user = Auth::user();
		
		$defaultPic = "https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/s%3D220/sign=13075a6735fae6cd08b4ac633fb30f9e/4bed2e738bd4b31c032d2feb87d6277f9e2ff849.jpg";
		if ( ! empty($user) && isset($user->name)  && $user->name == 'admins') {
			return response()->json(['code' => 401, 'message' => '角色错误'], 401);
		}
		$uid = isset($user->id) ? $user->id : 1;
		$userModel = new UserInfo();
		$relevantModel = new UserRelevantInfo();
		$uModel = new User();
		$fileModel = new File();
		$levelModel = new LevelConfig();
		$data = $userModel->getAdminInfo($uid);
		$uInfo = $uModel->where('id', $uid)->first();
		if ( ! empty($data['pic_id'])) {
			$file = File::selectRaw('dirType, name')->where('id', $data['pic_id'])->first();
		} else {
			$file = null;
		}
		$relevant = $relevantModel->where('uid', $uid)->first();
		$levelInfo = $levelModel->where('rank', $data['level_id'])->first();
		if ($file == null || !isset($file->name)) {
			$picture = $defaultPic;
		} else {
			$picture = $fileModel->checkHttps() . $_SERVER['SERVER_NAME']. "/" .$file->dirType . "/" . $file->name;
		}
		$levelFile = File::selectRaw('dirType, name')->where('id', $levelInfo['icon'])->first();

		$ret = [
			'uid' => $uid,
			'picture' => $picture,
			'name' => isset($uInfo->name) ? $uInfo->name : '未定义',
			'level' => isset($levelInfo->name) ? $levelInfo->name : "无", 
			'sex' => $data['sex'],
			'birthday' => empty($data['birthday']) ? "未填写" : $data['birthday'],
			'wechat' => $data['wechat'],
			'money' => isset($relevant->total_money) ? $relevant->total_money : 0,
			'mobile' => $data['mobile'],
			'usertype' => $data['user_type'],
			'picLevel' => $fileModel->checkHttps() . $_SERVER['SERVER_NAME']. "/" .$levelFile->dirType . "/" . $levelFile->name
		];
		return response()->json(['data' => $ret, 'code' => 200], 200);
	}
		
    /**
	 * 修改用户信息
	 *
	 * @SWG\Post(
	 *      path="/api/updateUser",
	 *      tags={"api-h5前端-wyg"},
	 *      summary="h5修改用户生日、性别、绑定微信",
	 *      operationId="updateUser",
	 *      security={
	 *          {
	 *              "api-token":{}
	 *          }
	 *      },
     *      @SWG\Parameter(
     *          in="formData",
     *          name="sex",
     *          type="integer",
	 *			description="性别1男2女"
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="picId",
     *          type="integer",
	 *			description="头像id"
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="birthday",
     *          type="string",
     *          description="生日",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="wechat",
     *          type="string",
     *          description="绑定微信账号",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="mobile",
     *          type="string",
     *          description="手机号",
     *      ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="用户名",
     *      ),
	 *      @SWG\Response(
	 *          response=401,
	 *          description="出错了",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 *                      property="msg",
	 *                      type="string", 
	 *                      description="提示消息，如:用户名已被使用"
	 *                  )
	 *              )
	 *			)
	 *      ),
	 *      @SWG\Response(
	 *          response="default",
	 *          description="修改用户信息",
	 *          @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(
	 *                  @SWG\Property(
	 *                      property="uid",
	 *                      type="string", 
	 *                      description="用户uid"
	 *                  )
	 *              )
	 *      )
	 *   )
	 * )
	 */
	public function updateUser(Request $request) {
		$updateArr = $userArr = [];
		$user = Auth::user();
		$uid = isset($user->id) ? $user->id : 1;

		//表单验证
        $rule = $msg = [];
        if(isset($request->name) && $request->name) {
            $rule['name'] = 'between:6,20|alpha_dash_chinese';
            $msg['name.between'] = '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文';
            $msg['name.alpha_dash_chinese'] = '用户名不正确,请输入6~20位的字符,可包含数字、字母、中文';
        }
        if(isset($request->mobile) && $request->mobile) {
            $rule['mobile'] = 'phone';
            $msg['mobile.phone'] = '手机号不正确';
        }
        $this->validator = Validator::make($request->all(), $rule, $msg);
        try{
            $this->validatorFails();
        }catch (\Exception $e) {
            return response()->json(['code' => 4001, 'msg' => $e->getMessage()]);
        }

		// 修改用户名
		if (isset($request->name) && ! empty($request->name)) {
			$uModel = new Users();
			if ($request->name != $user->name) {
				$exist = $uModel->selectRaw('id')->where('name', $request->name)->first();
				if ( ! isset($exist->id)) { // 用户名不存在，可以修改
					$userArr['updated_at'] = date('Y-m-d H:i:s');
					$userArr['name'] = $request->name;
					$uModel->updateUser($userArr, $uid);
				} else {
					return response()->json(['code' => 401, 'msg' => '用户名已被使用']);
				}
			}
		}
		
		if (isset($request->birthday) && ! empty($request->birthday)) { // 生日
			$updateArr['birthday'] = $request->birthday;
		}
		if (isset($request->sex)) {// 性别
			$updateArr['sex'] = $request->sex;
		}
		if (isset($request->wechat) && ! empty($request->wechat)) { // 绑定微信
			$updateArr['wechat'] = $request->wechat;
		}
		if (isset($request->picId) && ! empty($request->picId)) { // 头像id
			$updateArr['pic_id'] = $request->picId;
		}
		if (isset($request->mobile)) { // 绑定微信
			$updateArr['mobile'] = $request->mobile;
		}
		if (! empty($updateArr)) {
			$userModel = new UserInfo();
			$updateArr['updated_at'] = date('Y-m-d H:i:s');
			$userModel->updateUser($updateArr, $uid);
		}
		
		return response()->json(['success' => $uid, 'code' => 200], 200);
	}

	/**
     * 处理注册链接，获取层级、上级
     * */
    private function _dealInvite($registerUrl) {
        if(empty($registerUrl)) {
            return '';
        }
        /*$registerUrlArr = explode('?', $registerUrl);
        $domain = $registerUrlArr[0];
        $param = explode('&', $registerUrlArr[1]);
        $key = '';
        foreach($param as $v) {
            if( strpos($v, 'key=') !== false ) {
                $key = str_replace('key=', '', $v);
            }
        }
        if(!$domain || !$key) {
            return '';
        }*/
        $key = trim($registerUrl);

        //获取parent_uid
        $promoteConfig = PromoteConfig::where(['key' => $key])->first(['id', 'uid', 'account_type']);
        if(empty($promoteConfig)) {
            $host = $_SERVER["HTTP_HOST"];
            $model = new PromoteConfig();
            $promoteConfig = $model->getDefaultKey($host, ['id', 'uid', 'account_type']);
            if(empty($promoteConfig)) {
                return '';
            }
        }
        $userInfo = UserInfo::where('uid', $promoteConfig->uid)->first(['parent_uid_dir', 'hierarchy']);
        return [
            'promote_id'    => $promoteConfig->id,
            'uid'           => $promoteConfig->uid,
            'account_type'  => $promoteConfig->account_type,
            'hierarchy'     => $userInfo->hierarchy,
            'parent_uid_dir'=> $userInfo->parent_uid_dir,
        ];
    }



}

