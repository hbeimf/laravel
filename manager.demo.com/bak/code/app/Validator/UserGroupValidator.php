<?php

namespace App\Validator;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class UserGroupValidator extends LaravelValidator {


    protected $rules = [



        'updateStatus'=> [
            'id'=>'required|exists:bw_user_group,id|',  /*id*/
        ],

        'add'=> [
            'name'=>'required|between:min:1,30|unique:bw_user_group,name',  /*分组名称*/
            'begin_time'=>'date',    /*用户加入期间，开始时间*/
            'end_time'=>'date',  /*用户加入期间，结束时间*/
            'theme_limit'=>'digits_between:1,11|integer',   /*主题限制*/
            'deposit_time'=>'required|integer|digits_between:1,8',    /*存款次数*/
            'total_deposit_money'=>'required|digits_between:1,8|integer', /*存款总额，历次存款额度*/
            'max_deposit_money'=>'required|digits_between:1,11|integer',    /*最大存款金额*/
            'withdraw_time'=>'required|integer|digits_between:1,11',    /*提款次数*/
            'withdraw_money'=>'required|digits_between:1,8|integer',   /*提款总额*/
            'note'=>'required|',    /*备注*/
            'is_auto_group'=>'required|integer|between:0,1',     /*用户自动分组，0:关闭， 1：启用*/
        ],

        'update'=> [
            'name'=>'required|between:min:1,30|unique:bw_user_group,name',  /*分组名称*/
            'begin_time'=>'date',    /*用户加入期间，开始时间*/
            'end_time'=>'date',  /*用户加入期间，结束时间*/
            'theme_limit'=>'digits_between:1,11|integer',   /*主题限制*/
            'deposit_time'=>'required|integer|digits_between:1,8',    /*存款次数*/
            'total_deposit_money'=>'required|digits_between:1,8|integer', /*存款总额，历次存款额度*/
            'max_deposit_money'=>'required|digits_between:1,11|integer',    /*最大存款金额*/
            'withdraw_time'=>'required|integer|digits_between:1,11',    /*提款次数*/
            'withdraw_money'=>'required|digits_between:1,8|integer',   /*提款总额*/
            'note'=>'required|',    /*备注*/
            'is_auto_group'=>'required|integer|between:0,1',     /*用户自动分组，0:关闭， 1：启用*/
        ],



    ];

    protected $messages = [
        'uid.bw_user_info'=>'uid长度在1-10位数',
        'name.unique'=>'用户组已存在',
    ];
}