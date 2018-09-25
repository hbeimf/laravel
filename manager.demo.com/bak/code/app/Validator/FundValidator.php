<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class FundValidator extends LaravelValidator {


    protected $rules = [

        'create' => [
            'pay_id' => 'required|integer',
            'money'  => 'required|integer',
            'nick'=> 'required',
        ],

        'update' => [
            'user_name' => 'required',
            'bank_name'  => 'required|min:3',
            'bank_number'=> 'required'
        ],


    ];

    protected $messages = [
        'pay_id.required' => '请选择存款账号',
        'money.required' => '请输入充值金额',
        'money.integer' => '请输入正确的充值金额',
        'nick.required' => '请输入充值账号',
    ];
}