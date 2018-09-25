<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class BankListValidator extends LaravelValidator {

    protected $rules = [

        'create' => [
            'name'=> 'required',
        ],

        'update' => [
            'name'  => 'required',
        ],

    ];

    protected $messages = [
        'name.required'=> '请输入银行名称',
    ];
}