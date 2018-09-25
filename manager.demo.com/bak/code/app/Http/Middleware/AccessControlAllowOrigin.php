<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

/**
 * 跨域 中间件
 * */

class AccessControlAllowOrigin
{
    /**
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->addAccessHeader();

        return $next($request);
    }

    /**
     * 跨域
     * */
    private function addAccessHeader()
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $allowOrigin = \config('cross-http.api');
        if (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin: '. $origin);
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Content-type: application/json');
//            header("Access-Control-Allow-Headers: Content-Type,Access-Token,Accept,Authorization");
//            header("Access-Control-Allow-Headers: Content-Type,Access-Token");
//            header("Access-Control-Expose-Headers: *");
        }
    }

}
