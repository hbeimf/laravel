<?php
namespace App\Http\Model;

use App\User;
use jeremykenedy\LaravelRoles\Models\Role;
use \Illuminate\Database\Eloquent\Model;

class Roles extends Model {
	protected $table = 'roles';

	// protected $guarded = ['id', 'name', 'slug']; //黑名单，不会被更新
	protected $fillable = ['name', 'slug', 'description', 'level']; //开启白名单字段

	// public $timestamps = false;

	public function allRoles() {
		return $this->all();
	}

	public function getRoleById($id) {
		return $this->where('id', $id)->first();
	}

	public function getById($id) {
		return $this->where('id', '=', $id)->first();
	}

	public function getRoleByName($name) {
		return $this->where('name', '=', $name)->first();
	}

	public function getRoleBySlug($slug) {
		return $this->where('slug', '=', $slug)->first();
	}

	public function getAdmin() {
		$slug = 'admin';
		if (!$this->existsRole($slug)) {
			return $this->createRole('Admin', 'admin', 'admin role', 0);
		} else {
			return $this->getRoleBySlug($slug);
		}
	}

	public function existsRole($slug = '') {
		$obj = $this->where('slug', '=', $slug)->first();
		return is_object($obj);
	}

	public function existsRoleById($id, $slug = '') {
		$obj = $this->where('slug', '=', $slug)->where('id', '!=', $id)->first();
		return is_object($obj);
	}

	// 创建角色
	public function createRole($name, $slug, $desc = '', $level = 0) {
		if ($this->existsRole($slug)) {
			return $this->where('slug', '=', $slug)->first();
		} else {
			return $this->create([
				'name' => $name,
				'slug' => $slug,
				'description' => $desc,
				'level' => $level,
			]);
		}
	}

	// 更新角色
	public function updateRole($id, $name, $slug, $desc = '', $level = 0) {
		$row = [
			'name' => $name,
			'slug' => $slug,
			'description' => $desc,
			'level' => $level,
		];
		if (!$this->isSuperadmin($id)) {
			return $this->where('id', '=', $id)->update($row);
		}
	}

	public function isSuperadmin($id) {
		$row = $this->getRoleById($id);
		if (is_object($row) && $row->slug == 'superadmin') {
			return true;
		}
		return false;
	}

	public function getSuperadmin() {
		return $this->getRoleBySlug('superadmin');
	}

	// 删除角色
	public function del($id) {
		return $this->where('id', '=', $id)->delete();
	}

	// 账号与角色关联
	public function attachRole($userId, $roleId) {
		$user = $this->getUserById($userId);
		$role = $this->getRoleById($roleId);
		if (is_object($user) && is_object($role)) {
			$user->attachRole($role);
			return true;
		}
		return false;
	}

	// 账号与角色取消关联
	public function detachRole($userId, $roleId) {
		$user = $this->getUserById($userId);
		$role = $this->getRoleById($roleId);
		if (is_object($user) && is_object($role)) {
			$user->detachRole($role);
		}
	}

	//账号与角色取消所有关联
	public function detachAllRoles($userId) {
		$user = $this->getUserById($userId);
		if (is_object($user)) {
			$user->detachAllRoles();
		}
	}

	// 账号查看是否与某些角色关联
	public function hasRole($userId, $roleArray = ['admin', 'moderator', 'test']) {
		$user = $this->getUserById($userId);
		if (is_object($user)) {
			return ($user->hasRole($roleArray)) ? true : false;
		}
		return false;
	}

	private function getUserById($userId) {
		return User::where('id', '=', $userId)->first();
	}

	// 根据用户uid获取用户的角色
	public function getRoleByUid($uid) {
		$reply = [
			'id' => 0,
			'name' => '',
		];
		// $sql = "select b.id, b.name from role_user as a left join roles as b on a.role_id = b.id where a.user_id = {$uid}";
		// $obj = DB::select($sql);

		$select = 'roles.id, roles.name';
		$table = Role::selectRaw($select);
		$rows = $table
			->rightJoin('role_user as b', 'b.role_id', '=', 'roles.id')
			->where('b.user_id', '=', $uid)
			->first();

		if (is_object($rows)) {
			// return $rows->toArray();
			$reply = [
				'id' => $rows->id,
				'name' => $rows->name,
			];
		}
		return $reply;
	}

}