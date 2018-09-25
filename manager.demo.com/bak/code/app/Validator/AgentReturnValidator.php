<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class AgentReturnValidator extends LaravelValidator {


    protected $rules = [

        'create' => [
            'uid' => 'required|integer|unique:bw_forbid_return_point,uid|exists:bw_user_info,uid|digits_between:1,10',
        ],




    ];

    protected $messages = [
        'uid.bw_user_info'=>'uid长度在1-10位数',

    ];
}