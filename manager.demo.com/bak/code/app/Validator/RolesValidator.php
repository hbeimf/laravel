<?php

namespace App\Validator;

use \Prettus\Validator\LaravelValidator;

class RolesValidator extends LaravelValidator {
    
    protected $rules = [
        
        'create' => [
            'name' => 'required|min:3|unique:roles,name',
            'slug' => 'required|min:3|unique:roles,slug',
            
        ],
        
        'update' => [
            'name' => 'required|min:3|unique:roles,name',
            'slug' => 'required|min:3|unique:roles,slug',
        ],
        
    ];
    
    protected $messages = [
        'name.required' => '请输入角色名称',
        'name.unique' => '角色名称已存在',
        'slug.required' => '请输入资源唯一标识',
        'slug.unique' => '资源唯一标识已存在',
        
    ];
    
}
