<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class DepositConfigValidator extends LaravelValidator {


    protected $rules = [

        'create' => [
			'name' => 'required|min:3|unique:bw_deposit_config,name', // 模型名称
			'max_money' => 'required|numeric', // 单次最高存款金额
			'min_money' => 'required|numeric',  // 单次最低存款金额
			'discount_type' => 'required',  // 存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次
			'discount_time' => 'required_if:discount_type,3|integer', // 存款优惠次数
			'discount_giveup' => 'required_unless:discount_type,0',  // 用户是否可放弃优惠
			'discount_money' => 'required_unless:discount_type,0',  // 单次存款达到discount_money进行优惠
			'discount_proportion' => 'required_unless:discount_type,0',  // 优惠比例
			'discount_max_money' => 'required_unless:discount_type,0', // 单次优惠上限金额
			'ex_discount_type' => 'required',  // 额外存款优惠次数类型
			'ex_discount_time' => 'required_unless:ex_discount_type,0',  // 额外存款优惠次数
			'ex_discount_giveup' => 'required_unless:ex_discount_type,0',  // 额外用户是否可放弃优惠
			'ex_discount_proportion' => 'required_unless:ex_discount_type,0', // 额外优惠比例
			'ex_discount_money' => 'required_unless:ex_discount_type,0', // 额外单次存款达到多少进行优惠
			'is_enable' => 'required', // 常态性稽核打码量是否启用
			'proportion' => 'required_if:is_enable,1',  // 单次最高存款金额
			'relaxable' => 'required_if:is_enable,1', // 额外可放宽
			'administrative_rate' => 'required_if:is_enable,1', // 否则收取行政费率
			'ex_is_enable' => 'required', // 普通优惠稽核设定
			'code_checking' => 'required_if:ex_is_enable,1', // 综合打码量稽核
        ],

        'update' => [
			'name' => 'required|min:3|unique:bw_deposit_config,name', // 模型名称
			'max_money' => 'required|numeric', // 单次最高存款金额
			'min_money' => 'required|numeric',  // 单次最低存款金额
			'discount_type' => 'required',  // 存款优惠次数类型， 0:不优惠，1:首次，2:每次，3：前N次
			'discount_time' => 'required_if:discount_type,3|integer', // 存款优惠次数
			'discount_giveup' => 'required_unless:discount_type,0',  // 用户是否可放弃优惠
			'discount_money' => 'required_unless:discount_type,0',  // 单次存款达到discount_money进行优惠
			'discount_proportion' => 'required_unless:discount_type,0',  // 优惠比例
			'discount_max_money' => 'required_unless:discount_type,0', // 单次优惠上限金额
			'ex_discount_type' => 'required',  // 额外存款优惠次数类型
			'ex_discount_time' => 'required_unless:ex_discount_type,0',  // 额外存款优惠次数
			'ex_discount_giveup' => 'required_unless:ex_discount_type,0',  // 额外用户是否可放弃优惠
			'ex_discount_proportion' => 'required_unless:ex_discount_type,0', // 额外优惠比例
			'ex_discount_money' => 'required_unless:ex_discount_type,0', // 额外单次存款达到多少进行优惠
			'is_enable' => 'required', // 常态性稽核打码量是否启用
			'proportion' => 'required_if:is_enable,1',  // 单次最高存款金额
			'relaxable' => 'required_if:is_enable,1', // 额外可放宽
			'administrative_rate' => 'required_if:is_enable,1', // 否则收取行政费率
			'ex_is_enable' => 'required', // 普通优惠稽核设定
			'code_checking' => 'required_if:ex_is_enable,1|numeric', // 综合打码量稽核
        ],


    ];

    protected $messages = [
        'name.unique' => '模式名已存在',
		'discount_money.required_if' => '`单次存款达到多少进行优惠`不能为空',
		'max_money.required' => '单次最高存款金额不能为空',
		'max_money.numeric' => '单次最高存款金额必须为数字',
		'min_money.required' => '单次最低存款金额不能为空',
		'min_money.numeric' => '单次最低存款金额必须为数字',
		'discount_type.required' => '存款优惠次数类型不能为空',
		'discount_time.required_if' => '存款优惠次数不能为空',
		'discount_time.integer' => '存款优惠次数要为整数',
		'discount_giveup.required_unless' => '用户是否可放弃优惠不能为空',
		'discount_money.required_unless' => '单次存款达到多少进行优惠不能为空',
		'discount_proportion.required_unless' => '优惠比例不能为空',
		'discount_max_money.required_unless' => '单次优惠上限金额不能为空',
		'ex_discount_type.required' => '额外存款优惠次数类型不能为空',
		'ex_discount_time.required_unless' => '额外存款优惠次数不能为空',
		'ex_discount_giveup.required_unless' => '额外用户是否可放弃优惠不能为空',
		'ex_discount_proportion.required_unless' => '额外优惠比例不能为空',
		'ex_discount_money.required_unless' => '额外单次存款达到多少进行优惠不能为空',
		'is_enable.required' => '常态性稽核打码量是否启用不能为空',
		'proportion.required' => '单次最高存款金额不能为空',
		'relaxable.required' => '额外可放宽不能为空',
		'administrative_rate.required' => '行政费率不能为空',
		'ex_is_enable.required' => '普通优惠稽核设定不能为空',
		'code_checking.required_if' => '综合打码量稽核不能为空',
		'code_checking.numeric' => '综合打码量稽核为阿拉伯数字',
    ];
}