<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Fn;
use App\Http\Controllers\Controller;
use App\Http\Model\Permissions;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use \Logger;

class CommonController extends Controller {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

	protected $result = ['code' => 200, 'msg' => 'ok'];
	protected $validator;

	public function __construct() {
		$config = config('app.log_config_db');
		Logger::configure($config);
		$this->log = Logger::getLogger(__CLASS__);

		// 检查当前角色是否有权限访问此接口
		$this->middleware(function ($request, $next) {
			$this->user = Auth::user();
			if (is_object($this->user)) {
				if ($this->user->id > 0) {
					$tablePermission = new Permissions();
					// $uri = trim(\Request::getRequestUri(), '/');
					$uri = $this->getCurrentUri();
					$reply = $tablePermission->checkPermission($uri, $this->user->id);
					if (!$reply['flg']) {
						echo json_encode(['code' => $this->errorStatus, 'error' => '接口:[' . $uri . '],' . $reply['msg']]);
						exit;
					}
				}
			}
			return $next($request);
		});
	}

	public $successStatus = 200;
	public $errorStatus = 401;

	public function getCurrentUri() {
		$uri = \Request::getRequestUri();
		$arr = explode('?', $uri);
		$r = isset($arr[0]) ? $arr[0] : '';
		return trim($r, '/');
	}

	public static function getUid() {
		$id = Auth::id();
		if ($id) {
			return $id;
		}
		throw new \Exception('当前未登录', 4001);
	}

	/**
	 * 判断试玩用户
	 * */
	public static function mustNormalUser() {
		$userInfo = Auth::user();
		if ($userInfo) {
			if ($userInfo->type == \App\User::TYPE_TRY) {
				throw new \Exception('试玩用户请先注册', 4009);
			}
			return $userInfo->id;
		}
		throw new \Exception('当前未登录', 4001);
	}

	/**
	 * 表单验证错误输出
	 * */
	public function validatorFails() {
		if ($this->validator->fails()) {
			$errors = Fn::objToArray($this->validator->errors());
			foreach ($errors as $v) {
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

	/**
	 * @todo 向前台输出错误
	 * **/
	public function error($code = false, $message = false, $url = '') {
		$_error = [
			400 => '出错了!',
			403 => '没有权限访问!',
			404 => '页面访问不了!',
		];

		return response()->json(
			[
				'code' => $code > 0 ? $code : $this->errorStatus,
				'error' => $message ? $message : ($code > 0 && $_error[$code] != '' ? $_error[$code] : '出错了,请联系管理员!'),
				'url' => $url,
			],
			$this->errorStatus);
	}

	/**
	 * @todo 向前台发送数据
	 * **/
	public function success($data = [], $message = false, $url = '') {
		return response()->json(
			[
				'code' => $this->successStatus,
				'error' => $message ? $message : ($code > 0 && $_error[$code] ? $_error[$code] : '操作已经完成!'),
				'url' => $url,
			],
			$this->successStatus);
	}
}
