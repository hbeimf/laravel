<?php

namespace App\Http\Middleware;

use App\Helper\Fn;
use Closure;
use Validator;
use Illuminate\Routing\Route;

class ValidateMiddleware
{
    protected $validator;
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $controller = $this->getControllerName($request->route()->getActionName());
            $action = $this->getRouteName();
            $rule = $this->check($controller,$action);
            $this->validator = Validator::make($request->all(),$rule,$this->notices);
            $this->validatorFails();
        }
        catch (\Exception $e)
        {
            return response()->json(['code' => $this->errorStatus, 'success' => $e->getMessage()], $this->successStatus);
        }
        return $next($request);
    }
    
    protected function getControllerName($controller)
    {
        $args = explode('\\', $controller);
        return explode('@', array_last($args))[0];
    }
    
    protected function getRouteName()
    {
        $uri = \Request::getRequestUri();
        $args = explode('?', $uri);
        $args = explode('/v1/', $args[0]);
        if (count($args)<2) {
            $args = explode('/api/', $args[1]);
        }
        return $args[1];
    }
    
    protected function validatorFails() {
        if ($this->validator->fails()) {
            $errors = Fn::objToArray($this->validator->errors());
            foreach ($errors as $v) {
                throw new \Exception($v[0], 4001);
                break;
            }
        }
    }
    
    protected $rules = [
        'IcomeController'=>[
            'id'=>'required|exists:bw_inmoney,id',
            'status'=>'in:0,-1,1',
            'money_start'=>'numeric',
            'money_end'=>'numeric',
            'name'=>'string',
            'time_start'=>'date',
            'time_end'=>'date',
            'user_group'=>'exists:bw_user_group,id',
            'time_type'=>'in:0,1,2',
            'opt_id'=>'required|exists:users,id'
        ],
    ];
    
    protected $params = [
        'icome/list'=>['status','name','money_start','money_end','user_group','time_type','time_start','time_end'],
        'icome/ok'=>['id','opt_id'],
        'icome/cancel'=>['id','opt_id'],
    ];
    
    protected $notices = [
        'user_group.exists'=>'用户组ID不存在',
        'id.exists'=>'列表ID不存在',
        'opt_id.exists'=>'操作员ID不存在',
    ];
    
    protected function check($controller,$action)
    {
        $rules = $this->rules[$controller];
        $params = $this->params[$action];
        $rule = [];
        foreach ($params as $p_k=>$p_v) {
            foreach ($rules as $r_k=>$r_v) {
                if ($p_v==$r_k) {
                    $rule[$r_k] = $r_v;
                }
            }
        }
        return $rule;
    }
    
}
