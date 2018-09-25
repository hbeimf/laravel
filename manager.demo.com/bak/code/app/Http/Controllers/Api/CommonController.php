<?php

namespace App\Http\Controllers\Api;

use App\Helper\Fn;
use App\Http\Model\UserInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Logger;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;

class CommonController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;
    //
    protected $result = ['code' => 200, 'msg' => 'ok'];
    protected $validator;

    public function __construct(){
		$config = config('app.log_config_db');
        Logger::configure($config);
        $this->log = Logger::getLogger(__CLASS__);
    }

    public static function getUid() {
        $id = Auth::id();
        if($id) {
            return $id;
        }
        throw new \Exception('当前未登录', 4001);
    }

    /**
     * 判断试玩用户
     * */
    public static function mustNormalUser() {
        $user = Auth::user();
        $userInfo = UserInfo::where('uid', $user->id)->first(['user_type']);
        if($userInfo) {
            if($userInfo->user_type == UserInfo::USER_TYPE_TRY) {
                throw new \Exception('试玩用户请先注册', 4009);
            }
            return $user->id;
        }
        throw new \Exception('当前未登录', 4001);
    }

    /**
     * 表单验证错误输出
     * */
    public function validatorFails() {
        if ($this->validator->fails()) {
            $errors = Fn::objToArray($this->validator->errors());
            foreach($errors as $v) {
                throw new \Exception($v[0], 4001);
                break;
            }
        }
    }

    /**
     * 测试输出
     * */
    public function p($d) {
        echo '<pre>';
        print_r($d);
        echo '</pre>';
        exit;
    }
}
