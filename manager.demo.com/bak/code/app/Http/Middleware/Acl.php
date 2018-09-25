<?php

namespace App\Http\Middleware;

use App\Http\Model\Permissions;
use Closure;
use Illuminate\Support\Facades\Auth;

class Acl {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null) {
		$this->user = Auth::user();
		if (is_object($this->user)) {
			if ($this->user->id > 0) {
				$tablePermission = new Permissions();
				$uri = $this->getCurrentUri($request);
				$reply = $tablePermission->checkPermission($uri, $this->user->id);
				if (!$reply['flg']) {
// 					echo json_encode(['code' => $this->errorStatus, 'error' => '接口:[' . $uri . '],' . $reply['msg']]);
// 					exit;
				}
			}
		} else {
			echo "not login";exit;
		}

		return $next($request);
	}

	public function getCurrentUri($request) {
		$routeAction = $request->route()->getAction();

		if (isset($routeAction['as'])) {
			return $routeAction['as'];
		}

		$requestMethod = request()->getMethod();
		$uri = \Request::getRequestUri();
		$arr = explode('?', $uri);
		$r = isset($arr[0]) ? $arr[0] : '';
		$arr = explode('/', trim($r, '/'));
		if (is_numeric($arr[count($arr) - 1])) {
			$uri = str_replace('/' . $arr[count($arr) - 1], '', $r);
		}
		return $requestMethod . '/' . trim($uri, '/');
	}

	public $errorStatus = 403;

}
