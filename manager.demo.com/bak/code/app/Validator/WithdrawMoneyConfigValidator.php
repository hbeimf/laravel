<?php

namespace App\Validator;

use \Prettus\Validator\LaravelValidator;

class WithdrawMoneyConfigValidator extends LaravelValidator {

	protected $rules = [

		'create' => [
		    'name' => 'required|min:3|unique:bw_withdraw_money_config,name',
		],

		'update' => [
		    'name' => 'required|min:3|unique:bw_withdraw_money_config,name',
		],

	];

	protected $messages = [
		'name.required' => '请输入模式名称',
		'name.unique' => '模式名称已存在',
	];

}