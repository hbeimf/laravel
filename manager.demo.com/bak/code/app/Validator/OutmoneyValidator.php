<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class OutmoneyValidator extends LaravelValidator {

    protected $rules = [

        'create' => [
            'uid' => 'required',
        ],

        'update' => [
            'user_name' => 'required',
            'bank_name'  => 'required|min:3',
            'bank_number'=> 'required'
        ],

		'lock'	=> [
			'id'	=> 'required|int'
		],

        'refuse'	=> [
            'remark'	=> 'required'
        ],

        //前端申请提现
        'c_create' => [
            'bank_card_id' => 'required|numeric',
            'withdraw_money'  => 'required|numeric',
            'nick'=> 'required',
        ],
    ];

    protected $messages = [
        'remark.required' => '请输入备注信息',
    ];
}