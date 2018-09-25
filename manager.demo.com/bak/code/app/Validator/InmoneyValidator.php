<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class InmoneyValidator extends LaravelValidator {


    protected $rules = [

        'create' => [
            'pay_id' => 'required|integer',
            'money'  => 'required|integer',
            'nick'=> 'required',
			'in_model' => 'required|integer',
        ],

        'update' => [
            'user_name' => 'required',
            'bank_name'  => 'required|min:3',
            'bank_number'=> 'required'
        ],

        //前端充值
        'c_create' => [
            'pay_id' => 'required|numeric',
            'money'  => 'required|numeric',
            'nick'=> 'required',
        ],

        /**
         * 充值优惠
         * */
        'c_preferential' => [
            'money'  => 'required|numeric',
        ],
		
		'add' => [
			'in_type' => 'required',
			'money' => 'required|numeric',
			'uid' => 'required|integer'
		],


    ];

    protected $messages = [
        'pay_id.required' => '请选择存款账号',
        'money.required' => '请输入充值金额',
        'money.integer' => '请输入正确的充值金额',
        'nick.required' => '请输入充值账号',
		'in_model.required' => '请输入存款类型',
		'add.money' => '金额不能为空',
		'add.in_type' => '支付类型不能为空',
		'add.uid' => '存款用户id不能为空'
    ];
}