<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class BankCardValidator extends LaravelValidator {

    protected $rules = [

        'create' => [
            'uid' => 'required',
            'bank_name'=> 'required',
            'card_num'=> 'required',
            'bank_area'=> 'required',
            'bank_branch'=> 'required',
            'user_name'=> 'required',
            //'status'=> 'required',
           // 'is_default'=> 'required',
        ],

        'update' => [
            // 'uid' => 'required',
            //'bank_name'  => 'required',
            //'card_num'=> 'required'
        ],

    ];

    protected $messages = [
        'uid.required' => '请输入uid号',
        'bank_name.required'=> '请输入银行名称',
        'card_num.required' => '请输入银行账号',
    ];
}