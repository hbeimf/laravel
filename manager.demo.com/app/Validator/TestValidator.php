<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class TestValidator extends LaravelValidator {


    protected $rules = [

        'create' => [
            'user_name' => 'required',
            'bank_name'  => 'required|min:3',
            'bank_number'=> 'required'
        ],

        'update' => [
            'user_name' => 'required',
            'bank_name'  => 'required|min:3',
            'bank_number'=> 'required'
        ],


    ];

    protected $messages = [
        'user_name.required' => '请输入用户名',
        'bank_name.required' => '请输入开户银行',
        'bank_number.required' => '请输入银行账号',
    ];
}