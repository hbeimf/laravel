<?php
// https://www.jianshu.com/p/2988ba405b3b?from=timeline&isappinstalled=0
// https://github.com/jeremykenedy/laravel-roles

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CommonController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;


use App\Http\Model\Roles;
use App\Http\Model\Permissions;

class AclController extends CommonController
{

	/**
	* 
	* http://code.demo.com/api/create_role
	 *
	*/
	public function createRole() {
		$this->log->debug("创建角色");

		$params = array_map("trim", [
			'name' => request('name'),
			'slug' => request('slug'),
			'desc' => '',
			'level' => 0,
		]);

		if ($params['name'] == '') {
			return response()->json(['code'=> $this->errorStatus,
				'error'=>'name不能为空！'], $this->successStatus);
		} elseif($params['slug'] == '') {
			return response()->json(['code'=> $this->errorStatus,
				'error'=>'slug 不能为空！'], $this->successStatus);
		}

		$table_role = new Roles();
		$role = $table_role->createRole($params['name'], $params['slug'],
			$params['desc'], $params['level']);

		return response()->json(['code' => $this->successStatus,
			'success'=> $role], $this->successStatus);
	}


	// create api | create permissions
	public function createPermission() {
		$this->log->debug("创建资源");

		$params = array_map("trim", [
			'name' => request('name'),
			'slug' => request('slug'),
			'desc' => '',
		]);

		if ($params['name'] == '') {
			return response()->json(['code'=> $this->errorStatus,
				'error'=>'name不能为空！'], $this->successStatus);
		} elseif($params['slug'] == '') {
			return response()->json(['code'=> $this->errorStatus,
				'error'=>'slug 不能为空！'], $this->successStatus);
		}


		$t = new Permissions();
		$permission = $t->createPermission($params['name'], $params['slug'],
			$params['desc']);

		return response()->json(['code' => $this->successStatus,
			'success'=> $permission], $this->successStatus);

	}

	public function attachRoleAndPermission() {
		$this->log->debug("绑定角色与资源关系！");


	}
}

