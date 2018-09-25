<?php
namespace App\Http\Model;

use App\User;
use jeremykenedy\LaravelRoles\Models\Role;
use \Illuminate\Database\Eloquent\Model;

class Permissions extends Model {
	protected $table = 'permissions';

	// public $timestamps = false;

	protected $fillable = ['name', 'slug', 'description', 'model']; //开启白名单字段

	// 检查用户是否对某个接口有访问权限
	public function checkPermission($uri, $uid) {
		$reply = [
			'flg' => true,
			'msg' => '',
		];
		$slug = str_replace('/', '.', $uri);

		if (!$this->existsPermission($slug)) {
			$reply = [
				'flg' => false,
				'msg' => '没有添加资源 ' . $slug,
			];
		} else {
			if (!$this->hasPermission($uid, $slug)) {
				$reply = [
					'flg' => false,
					'msg' => "uid:[{$uid}],slug:[{$slug}]没有访问权限",
				];
			}

		}

		return $reply;
	}

	// 获取角色下面的权限资源
	public function getPermissionByRole($roleId) {
		// permission_role,  permissions
		$select = "permissions.id,
			permissions.name,
			permissions.slug,
			permissions.description as `desc`,
			permissions.created_at,
			permissions.updated_at";

		$table = $this->selectRaw($select);
		$rows = $table
			->leftJoin('permission_role as b', 'b.permission_id', '=', 'permissions.id')
			->where('b.role_id', '=', $roleId)
			->get();

		return $rows;
	}

	public function getRowById($id) {
		return $this->where('id', $id)->first()->toArray();
	}

	public function allPermissions() {
		$select = 'id, name, slug, description as `desc`, created_at, updated_at';
		return $this->selectRaw($select)->get();
		// return $this->all();
	}

	public function existsPermission($slug) {
		$obj = $this->where('slug', '=', $slug)->first();
		return is_object($obj);
	}

	public function existsPermissionById($id, $slug = '') {
		$obj = $this->where('slug', '=', $slug)->where('id', '!=', $id)->first();
		return is_object($obj);
	}

	// 创建api 资源
	public function createPermission($name = 'Create users', $slug = 'create.users', $desc = '') {
		// $slug = strtolower($slug);

		$permission = $this->where('slug', '=', $slug)->first();
		if (is_object($permission)) {
			return $permission;
		} else {
			return $this->create([
				'name' => $name,
				'slug' => $slug,
				'description' => $desc, // optional
			]);
		}
	}

	// 更新
	public function updatePermission($id, $name, $slug, $desc = '', $level = 0) {
		return $this->where('id', '=', $id)->update([
			'name' => $name,
			'slug' => $slug,
			'description' => $desc,
		]);
	}

	// 删除
	public function del($id) {
		return $this->where('id', '=', $id)->delete();
	}

	// 资源与角色建立关联
	public function attachRoleAndPermission($permissionId, $roleId) {
		$permission = $this->getPermissionById($permissionId);
		$role = Role::find($roleId);
		if (is_object($permission) && is_object($role)) {
			$role->attachPermission($permission);
		}
	}

	// 资源与角色取消关联
	public function detachRoleAndPermission($permissionId, $roleId) {
		$permission = $this->getPermissionById($permissionId);
		$role = Role::find($roleId);
		if (is_object($permission) && is_object($role)) {
			$role->detachPermission($permission);
		}
	}

	// 角色取消所有的资源关联
	public function detachAllPermissions($roleId) {
		$role = Role::find($roleId);
		if (is_object($role)) {
			$role->detachAllPermissions();
		}
	}

	public function hasPermission($userId, $permissionSlugOrId) {
		$user = $this->getUserById($userId);

		if (is_object($user) && $user->hasPermission($permissionSlugOrId)) {
			// you can pass an id or slug
			return true;
		}
		return false;
	}

	public function getPermissionById($permissionId) {
		return $this->where('id', '=', $permissionId)->first();
	}

	public function getPermissionByName($name) {
		return $this->where('name', '=', $name)->first();
	}

	public function getPermissionBySlug($slug) {
		return $this->where('slug', '=', $slug)->first();
	}

	private function getUserById($userId) {
		return User::where('id', '=', $userId)->first();
	}

}