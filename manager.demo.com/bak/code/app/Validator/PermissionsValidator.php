<?php

namespace App\Validator;

use \Prettus\Validator\LaravelValidator;

class PermissionsValidator extends LaravelValidator {
    
    protected $rules = [
        
        'create' => [
            'name' => 'required|min:3|unique:permissions,name',
            'slug' => 'required|min:3|unique:permissions,slug',
            
        ],
        
        'update' => [
//             'id' => 'required',
            'name' => 'required|min:3|unique:permissions,name',
            'slug' => 'required|min:3|unique:permissions,slug',
        ],
        
    ];
    
    protected $messages = [
//         'id.required' => 'id不能为空',
        'name.required' => '请输入权限名称',
        'name.unique' => '权限名称已存在',
        'slug.required' => '请输入资源唯一标识',
        'slug.unique' => '资源唯一标识已存在',
        
    ];
    
}