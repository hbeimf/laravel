<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class AdmissionValidator extends LaravelValidator {


    protected $rules = [



        'updateStatus'=> [
            'id'=>'required|exists:bw_admission,id|',  /*id*/
        ],

        'create'=> [
            'bank' => 'required',//银行名称
            'number' => 'required|numeric|digits_between:1,20',//银行卡号码
            'bank_name' => 'required',//开户行名称
            'user_name' => 'required',//用户名称
            'sort' => 'required|integer|between:1,127',//排序
            'city' => 'required',//城市
            'province' => 'required',//省份
            'group' => 'required'//分组
        ],

        'update'=> [
        ],



    ];

    protected $messages = [
        'uid.bw_user_info'=>'uid长度在1-10位数',
        'name.unique'=>'用户组已存在',
    ];
}