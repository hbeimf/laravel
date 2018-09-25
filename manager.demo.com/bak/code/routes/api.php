<?php

/*
 |--------------------------------------------------------------------------
 | API Routes
 |--------------------------------------------------------------------------
 |
 | Here is where you can register API routes for your application. These
 | routes are loaded by the RouteServiceProvider within a group which
 | is assigned the "api" middleware group. Enjoy building your API!
 |
 */

//Dingo API
use Dingo\Api\Routing\Router;

$api = app(Router::class);
$api->version('v1', ['namespace' => 'App\\Http\\Controllers\\Admin\\V1'], function (Router $api) {
    
    $api->group(['middleware' => 'auth:api'], function (Router $api) {
        
        $api->get('test', 'TestController@listTest');
        $api->get('test/{id}', 'TestController@getTest');
        $api->post('test', 'TestController@createTest');
        $api->put('test/{id}', 'TestController@updateTest');
        $api->delete('test/{id}', 'TestController@destroy');
        
        //稽核查询
        $api->get('audit', 'AuditController@get');
        
        //Outmoney 出款操作
        $api->get('outmoney', 'OutmoneyController@listRecord');  //出款记录列表
        $api->get('outmoney/{id}', 'OutmoneyController@getRecord')->where('id', '[0-9]+'); //获取单个出款记录 ;
        $api->put('outmoney/{id}/lock', 'OutmoneyController@lockRecord')->where('id', '[0-9]+'); ; //锁定单个出款记录 ;
        $api->put('outmoney/{id}/confirm', 'OutmoneyController@confirmRecord')->where('id', '[0-9]+'); ; //手动出款 ;
        $api->put('outmoney/{id}/cancel', 'OutmoneyController@lockRecord')->where('id', '[0-9]+'); ; //取消，即取消该提现申请，取消后，冻结的提现款返回到用户账号 ;
        $api->put('outmoney/{id}/refuse', 'OutmoneyController@refuseRecord')->where('id', '[0-9]+'); ; //.拒绝，即提现扣款成功了，但是款项不会再返回给用户账户 ;
        $api->patch('outmoney/{id}/remark', 'OutmoneyController@updatRemark')->where('id', '[0-9]+'); ; //创建备注 ;
        $api->post('outmoney', 'OutmoneyController@createRecord'); //人工提出款项 ;
        $api->get('outmoneyType', 'OutmoneyController@outmoneyType'); //人工提出款项类型 ;
        
        //Inmoney 入款 操作
        $api->get('inmoney', 'InmoneyController@lists');
        $api->get('inmoney/{id}', 'InmoneyController@get');
        $api->post('inmoney', 'InmoneyController@create');
        $api->put('inmoney/{id}', 'InmoneyController@lock');
        $api->put('inmoney/confirm/{id}', 'InmoneyController@confirm');
        $api->put('inmoney/{id}', 'InmoneyController@rebut');
        
        // 取款模式
        $api->get('withdrawMoney', 'WithdrawmoneyController@list')->middleware('acl');
        $api->get('withdrawMoney/{id}', 'WithdrawmoneyController@get')->middleware('acl');
        $api->post('withdrawMoney', 'WithdrawmoneyController@create')->middleware('acl');
        $api->put('withdrawMoney/{id}', 'WithdrawmoneyController@update')->middleware('acl');
        $api->delete('withdrawMoney/{id}', 'WithdrawmoneyController@destroy')->middleware('acl');
        $api->get('withdrawMoneyAll', ['as' => 'getAllWithdrawMoneyAll', 'uses' => 'WithdrawmoneyController@all'])->middleware('acl');
        
        //存款模式
        $api->get('deposit', 'DepositConfigController@depositList')->middleware('acl'); //存款模式列表
        $api->get('getDeposit/{id}', 'DepositConfigController@getDeposit')->middleware('acl'); //获取单个存款模式模型
        $api->get('allDeposit', 'DepositConfigController@allDeposit')->middleware('acl'); //获取所有存款模式模型
        $api->post('deposit/add', 'DepositConfigController@addDeposit')->middleware('acl'); // 添加存款模式模型
        $api->post('deposit/update/{id}', 'DepositConfigController@updateDeposit')->middleware('acl'); // 修改存款模式模型
        $api->delete('deposit/delete/{id}', 'DepositConfigController@delete')->middleware('acl'); // 删除模型
        //人工存入
        $api->post('userInfo', 'UserInfoController@getUser'); // 人工存入，获取用户信息
        $api->post('addInmoney', 'InmoneyController@addInmoney'); // 人工存入
        $api->post('getDiscount', 'InmoneyController@getDiscount'); // 人工存入根据钱数获取优惠
        $api->post('getAudit', 'InmoneyController@getAudit'); // 人工存入根据钱数获取打码量
        $api->get('manualDepositType', 'InmoneyController@manualDepositType'); // 人工存入的几种固定类型
        
        //禁止代理返点
        $api->get('getAgents/{name}', 'AgentReturnController@getAgents'); //查询代理
        $api->get('agentRebate', 'AgentReturnController@listAgentReturn'); //禁止代理返点列表
        $api->post('agentRebate', 'AgentReturnController@addAgentRebate'); //添加禁止代理返点
        $api->delete('agentRebate/{id}', 'AgentReturnController@deleteAgentRebate'); //删除禁止代理返点
        //线下入款列表
        $api->get('icome/list', 'IcomeController@getList')->middleware('validate');  // 获取线下入款列表
        $api->post('icome/ok', 'IcomeController@okOrder')->middleware('validate');   // 确认入款订单
        $api->post('icome/cancel', 'IcomeController@cancelOrder')->middleware('validate');    // 取消入款订单
        
        //用户分组管理
        $api->get('userGroup', 'UserGroupController@listGroup'); //用户分组管理列表
        $api->get('userGroup/{id}', 'UserGroupController@get');     //用户单个分组详情
        $api->put('userGroupStatus/{id}', 'UserGroupController@updateStatus');  //用户分组自动分组锁定状态修改
        $api->post('userGroup', 'UserGroupController@add');    //新增单个分组
        $api->delete('userGroup/{id}', 'UserGroupController@delete');   //删除单个分组
        
        //银行卡管理
        $api->get('bankcard/{uid}', 'BankCardController@listRecord');  //用户银行卡列表
        $api->post('addBankcard', 'BankCardController@addBankCard');  //增加用户银行卡
        $api->post('updateBankcard', 'BankCardController@updateBankCard');  //更新用户银行卡
        $api->post('enableBankcard', 'BankCardController@enableBankCard');  //更改银行卡状态
        $api->post('deleteBankcard', 'BankCardController@deleteBankCard');  //删除用户银行卡
        
        $api->get('bankList', 'BankListController@listRecord');  //可选银行列表
        $api->post('addBank', 'BankCardController@addBank');  //增加银行
        $api->post('updateBank', 'BankCardController@updateBank');  //更新银行
        $api->post('deleteBank', 'BankCardController@deleteBank');  //删除银行
        
        //permissions 资源管理{重构}
        $api->get('permissions', ['as'=> 'getPermissions', 'uses'=> 'PermissionsController@list'])->middleware('acl');  //资源管理列表
        $api->get('permissions/{id}', ['as'=> 'getOnePermissions', 'uses'=> 'PermissionsController@get'])->middleware('acl');  //资源管理列表
        $api->post('permissions', ['as'=> 'createPermissions', 'uses'=> 'PermissionsController@create'] )->middleware('acl');
        $api->put('permissions/{id}', ['as'=> 'updatePermissions', 'uses'=> 'PermissionsController@update'])->middleware('acl');
        $api->delete('permissions/{id}', ['as'=> 'deletePermissions', 'uses'=> 'PermissionsController@destroy'] )->middleware('acl');
        $api->get('permissionsAll', ['as' => 'getPermissionsAll', 'uses' => 'PermissionsController@all'])->middleware('acl');
        
        //roles 角色管理{重构}
        $api->get('roles', ['as'=> 'getRoles', 'uses'=> 'RolesController@list'])->middleware('acl');  //角色管理列表
        $api->get('roles/{id}', ['as'=> 'getOneRoles', 'uses'=> 'RolesController@get'])->middleware('acl');
        $api->post('roles', ['as'=> 'createRoles', 'uses'=> 'RolesController@create'] )->middleware('acl');
        $api->put('roles/{id}', ['as'=> 'updateRoles', 'uses'=> 'RolesController@update'])->middleware('acl');
        $api->delete('roles/{id}', ['as'=> 'deleteRoles', 'uses'=> 'RolesController@destroy'] )->middleware('acl');
        $api->get('rolesAll', ['as' => 'getRolesAll', 'uses' => 'RolesController@all'])->middleware('acl'); 
        
        //收款账户管理
        $api->get('admission', 'AdmissionController@listAdmission');  //收款账户列表
        $api->get('admission/{id}', 'AdmissionController@get');     //收款账户单个账户详情
        $api->post('admission', 'AdmissionController@add');         //添加收款账户
        $api->put('admission/{id}', 'AdmissionController@update');         //修改收款账户
        $api->put('admissionStatus/{id}', 'AdmissionController@updateStatus');         //修改收款账户启用状态
        $api->delete('admission/{id}', 'AdmissionController@delete');
    });
});
    
    /*-------前端----------*/
    $api->version('v1', ['namespace' => 'App\\Http\\Controllers\\Api\\V1' ], function (Router $api) {
        //    $api->group(['middleware' => 'auth:api'], function (Router $api) {
        $api->get('client/admission', 'FundController@listAdmission'); //获取充值方式
        $api->get('client/admission_child/{type}', 'FundController@listAdmissionChild');   //充值方式下列表
        $api->get('client/admission/{id}', 'FundController@getAdmission'); //具体数据
        $api->get('client/deposit', 'FundController@getDeposit');   //获取充值区间
        $api->post('client/inmoney', 'FundController@createInmoney');  //充值
        $api->get('client/inmoney_pre', 'FundController@getInmoneyPreferential');  //充值优惠
        $api->post('client/outmoney', 'FundController@createOutmoney');  //提现
        $api->get('client/outmoney_pre', 'FundController@getOutmoneyPreferential');  //提现扣减
        $api->get('client/money_log', 'FundController@listMoneyLog'); //交易明细
        
        $api->get('client/is_bind', 'WalletController@isBind');    //是否绑定银行卡
        
        //前端返点重构
        $api->get('client/getGameList', 'SubuserController@getGameList');
        $api->post('client/addGameUrl', 'SubuserController@addGameUrl');
        $api->get('client/urlList', 'SubuserController@urlList');
        $api->get('client/editUrlList', 'SubuserController@editUrlList');
        $api->post('client/saveUrlEdit', 'SubuserController@saveUrlEdit');
        $api->get('client/delUrl', 'SubuserController@delUrl');
        $api->post('client/getSubser', 'SubuserController@getSubser');
        //    });
    });
        
        
        
        Route::post('login', 'Api\UserController@login'); // 登录
        Route::post('register', 'Api\UserController@register'); // 注册
        Route::post('guestRegister', 'Api\UserController@guestRegister'); // 试玩注册
        Route::get('getCaptcha', 'Api\UserController@getCaptcha'); // 获取验证码
        Route::get('getDefaultUrl', 'Api\SubuserController@getDefaultUrl'); // 获取默认推广链接
        // 前端接口------------------------------------------------------------------
        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('upload', 'UploadController@upload'); // 文件上传
            Route::post('changePassword', 'Api\UserController@changePassword'); // 更改密码
            Route::get('loginOut', 'Api\UserController@loginOut'); // 退出登录
            
            Route::get('wallet/getBankList', 'Api\WalletController@getBankList'); // 获取平台支持的银行
            Route::post('wallet/bindCard', 'Api\WalletController@bindCard'); // 绑定卡包
            Route::post('wallet/checkPassword', 'Api\WalletController@checkPassword'); // 校验交易密码
            Route::post('wallet/setDefaultCard', 'Api\WalletController@setDefaultCard'); // 设置默认卡
            Route::get('wallet/getCardList', 'Api\WalletController@getCardList'); // 获取我的卡包
            Route::get('wallet/isSetTradinglPwd', 'Api\WalletController@isSetTradinglPwd'); // 是否设置过交易密码
            Route::post('wallet/delCard', 'Api\WalletController@delCard'); // 删除我的卡包
            Route::post('wallet/setTradinglPwd', 'Api\WalletController@setTradinglPwd'); // 设置交易密码
            Route::post('wallet/changeTradinglPwd', 'Api\WalletController@changeTradinglPwd'); // 更改交易密码
            
            Route::match(['get', 'post'], 'userinfo', 'Api\UserController@getUserInfo');
            Route::post('updateUser', 'Api\UserController@updateUser');
            Route::get('h5/getGameList', 'Api\SubuserController@getGameList');
            Route::post('h5/addGameUrl', 'Api\SubuserController@addGameUrl');
            Route::get('h5/urlList', 'Api\SubuserController@urlList');
            Route::get('h5/editUrlList', 'Api\SubuserController@editUrlList');
            Route::post('h5/saveUrlEdit', 'Api\SubuserController@saveUrlEdit');
            Route::get('h5/delUrl', 'Api\SubuserController@delUrl');
            Route::post('h5/getSubser', 'Api\SubuserController@getSubser');
        });
            
            // acl ------------------------------------------------------------------
            Route::post('createRole', 'Admin\AclController@create_role'); // 创建角色
            // 后端接口------------------------------------------------------------------
            Route::get('test', 'Admin\TestController@index'); // test
            Route::post('adminLogin', 'Admin\UserController@adminLogin'); // 管理员登录
            Route::post('adminRegister', 'Admin\UserController@adminRegister'); // 管理员注册
            // -----------------------------------------------------------------------------------------
            // menu
            Route::group(['middleware' => 'auth:api'], function () {
                // -----------------------------------------------------------------------------------------
                // menu
                
                Route::post('getUserInfo', 'Admin\MenuController@getUserInfo'); // 获取导航
                Route::post('getMenuAll', 'Admin\MenuController@getMenuAll'); // 获取导航
                Route::post('getMenu', 'Admin\MenuController@getMenu'); // 获取导航分页
                Route::post('getMenuInner', 'Admin\MenuController@getMenuInner'); // 获取内部导航分页
                
                Route::post('createMenu', 'Admin\MenuController@createMenu'); // 创建导航
                Route::post('updateMenu', 'Admin\MenuController@updateMenu'); // 更新导航
                Route::post('delMenu', 'Admin\MenuController@delMenu'); // 删除导航
                Route::post('createMenuInner', 'Admin\MenuController@createMenuInner'); // 创建页面内部导航
                Route::post('updateMenuInner', 'Admin\MenuController@updateMenuInner'); // 更新页面内部导航
                Route::post('delMenuInner', 'Admin\MenuController@delMenuInner'); // 删除页面内部导航
                // -----------------------------------------------------------------------------------------
                // permission
                Route::post('createPermission', 'Admin\PermissionController@create'); // 创建权限资源
                Route::post('delPermission', 'Admin\PermissionController@del'); // 删除权限资源
                Route::post('updatePermission', 'Admin\PermissionController@update'); // 更新权限资源
                Route::post('getPermission', 'Admin\PermissionController@get'); // 获取权限资源
                Route::post('getPermissionAll', 'Admin\PermissionController@getAll'); // 获取全部权限资源
                Route::post('hasPermission', 'Admin\PermissionController@has'); // 检测用户是否有权限访问权限资源
                // -----------------------------------------------------------------------------------------
                // role
                Route::post('createRole', 'Admin\RoleController@create'); // 创建角色
                Route::post('delRole', 'Admin\RoleController@del'); // 删除角色
                Route::post('updateRole', 'Admin\RoleController@update'); // 更新角色
                Route::post('getRole', 'Admin\RoleController@get'); // 获取角色分页
                Route::post('getRoleAll', 'Admin\RoleController@getAll'); // 获取角色
                Route::post('attachRoleAndUser', 'Admin\RoleController@attachRoleAndUser'); // 关联角色和用户
                Route::post('detachRoleAndUser', 'Admin\RoleController@detachRoleAndUser'); // 取消关联角色和用户
                Route::post('attachRoleAndPermission', 'Admin\RoleController@attachRoleAndPermission'); // 关联角色和资源
                Route::post('detachRoleAndPermission', 'Admin\RoleController@detachRoleAndPermission'); // 取消关联角色和资源
                // ----------------------------------------------------------------------------------------
                // admin user
                Route::post('getAdmin', 'Admin\UserController@getAdmin'); // 获取管理员
                Route::post('adminUpdate', 'Admin\UserController@adminUpdate'); // 管理员信息修改
                Route::post('adminUpdatePassword', 'Admin\UserController@adminUpdatePassword'); // 管理员改密码
                Route::post('adminEnable', 'Admin\UserController@adminEnable'); // 启用禁用管理员
                // -----------------------------------------------------------------------------------------
                //推广链接
                //Route::post('admPromote/add', 'Admin\PromoteController@add');         // 新增推广链接
                //    Route::post('admPromote/edit', 'Admin\PromoteController@edit');         // 编辑推广链接
                //    Route::get('admPromote/getPromote', 'Admin\PromoteController@getPromote');// 获取推广链接列表
                //    Route::post('admPromote/getPromoteLink', 'Admin\PromoteController@getPromoteLink');// 获取推广链接
                //    Route::post('admPromote/delPromote', 'Admin\PromoteController@delPromote');// 删除推广链接
                //    Route::post('admPromote/delPromote', 'Admin\PromoteController@delPromote');// 删除推广链接
                Route::get('admPromote/details', 'Admin\PromoteController@details'); // 获取推广链接的详情
                Route::post('admPromote/save', 'Admin\PromoteController@save'); // 保存推广链接的信息
                Route::get('admPromote/list', 'Admin\PromoteController@getList'); // 新增推广链接列表
                Route::get('admPromote/getLink', 'Admin\PromoteController@getLink'); // 获取推广链接 的推广地址
                Route::post('admPromote/del', 'Admin\PromoteController@del'); // 删除一条推广链接
                //配置
                Route::get('admConfig/getGame', 'Admin\ConfigController@getGame'); // 获取游戏种类配置
                Route::get('admConfig/getGameMaxPoint', 'Admin\ConfigController@getGameMaxPoint'); // 获取游戏种类及返点上限配置
                Route::get('admConfig/getDomain', 'Admin\ConfigController@getDomain'); // 获取推广域名
                
                Route::get('userlevel/list', 'Admin\UserlevelController@index'); // 获取用户等级配置的详情
                Route::get('userlevel/details', 'Admin\UserlevelController@details'); // 获取用户等级配置的详情
                
                Route::post('userlevel/save', 'Admin\UserlevelController@save'); // 修改用户等级配置的内容
                Route::post('userlevel/del', 'Admin\UserlevelController@del'); // 删除一条配置
                Route::get('userlevel/users', 'Admin\UserlevelController@users'); // 删除一条配置
                //用户列表相关
                Route::post('getUserList', 'Admin\ClientController@getUserList'); //用户列表
                Route::post('upUserStatus', 'Admin\ClientController@upUserStatus'); //会员状态控制
                Route::get('getUserGameList', 'Admin\ClientController@getUserGameList'); //新增代理会员
                Route::post('saveUserInfo', 'Admin\ClientController@saveUserInfo'); //新增代理会员(提交)
                Route::post('userBsInformation', 'Admin\ClientController@userBsInformation'); //用户基本资料
                Route::post('userCpInformation', 'Admin\ClientController@userCpInformation'); //用户完整资料
                Route::post('saveUserBsInformation', 'Admin\ClientController@saveUserBsInformation'); //更改用户基本资料
                Route::post('saveUserCpInformation', 'Admin\ClientController@saveUserCpInformation'); //更改用户完整资料
                Route::post('getUserLowerList', 'Admin\ClientController@getUserLowerList'); //用户下级列表
                Route::post('getGameReturnPoint', 'Admin\ClientController@getGameReturnPoint'); //返点查看
                Route::post('upGameReturnPoint', 'Admin\ClientController@upGameReturnPoint'); //返点编辑
                Route::get('getUserHierarchy', 'Admin\ClientController@getUserHierarchy'); //层级查看
                //收款账户
                /*Route::get('getAdmissions', 'Admin\AdmissionController@getAdmissions'); //线下入款账户列表
                 Route::get('getAdmission', 'Admin\AdmissionController@getAdmission'); //线下入款账户列表
                 Route::post('admission', 'Admin\AdmissionController@addAdmission'); //添加线下入款账户
                 Route::post('updateAdmission', 'Admin\AdmissionController@updateAdmission'); //修改线下入款账户
                 Route::post('statusAdmission', 'Admin\AdmissionController@statusAdmission'); //修改线下入款账户状态
                 Route::post('deleteAdmission', 'Admin\AdmissionController@deleteAdmission'); //删除线下入款账户*/
                
                Route::post('userlevel/save', 'Admin\UserlevelController@save'); // 修改用户等级配置的内容
                Route::post('userlevel/del', 'Admin\UserlevelController@del'); // 删除一条配置
                Route::get('userlevel/users', 'Admin\UserlevelController@users'); // 删除一条配置
                //用户列表相关
                Route::post('getUserList', 'Admin\ClientController@getUserList'); //用户列表
                Route::post('upUserStatus', 'Admin\ClientController@upUserStatus'); //会员状态控制
                Route::get('getUserStatus', 'Admin\ClientController@getUserStatus'); //会员状态日志
                Route::get('getUserGameList', 'Admin\ClientController@getUserGameList'); //新增代理会员
                Route::post('saveUserInfo', 'Admin\ClientController@saveUserInfo'); //新增代理会员(提交)
                Route::post('userBsInformation', 'Admin\ClientController@userBsInformation'); //用户基本资料
                Route::post('userCpInformation', 'Admin\ClientController@userCpInformation'); //用户完整资料
                Route::post('saveUserBsInformation', 'Admin\ClientController@saveUserBsInformation'); //更改用户基本资料
                Route::post('saveUserCpInformation', 'Admin\ClientController@saveUserCpInformation'); //更改用户完整资料
                Route::post('getUserLowerList', 'Admin\ClientController@getUserLowerList'); //用户下级列表
                Route::post('getGameReturnPoint', 'Admin\ClientController@getGameReturnPoint'); //返点查看
                Route::post('upGameReturnPoint', 'Admin\ClientController@upGameReturnPoint'); //返点编辑
                Route::get('getUserHierarchy', 'Admin\ClientController@getUserHierarchy'); //层级查看
            });
                
                //swagger接口测试；生产环境可删除
                Route::group(['middleware' => ['auth:api', 'swfix']], function () {
                    
                });