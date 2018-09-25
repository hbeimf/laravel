<?php
namespace App\Http\Model;

// use Illuminate\Database\Capsule\Manager as DB;
use \Illuminate\Database\Eloquent\Model;

class Menusub extends Model {
	protected $table = 's_menu_sub';

	// public $timestamps = false;

	protected $fillable = ['menu_id', 'permission_id', 'controller', 'api_link', 'is_enable', 'description']; //开启白名单字段

	public function getRowById($id) {
		return $this->where('id', $id)->first()->toArray();
	}

	public function getMenuById($id) {
		$select = 's_menu_sub.id,
			s_menu_sub.menu_id,
			s_menu_sub.permission_id,
			s_menu_sub.controller,
			s_menu_sub.api_link,
			s_menu_sub.is_enable,
			s_menu_sub.description as `desc`,
			s_menu_sub.created_at,
			s_menu_sub.updated_at,
			b.name as menu_name,
			c.name as permission_name';

		$rows = $this->selectRaw($select)
			->leftJoin('s_menu as b', 'b.id', '=', 's_menu_sub.menu_id')
			->leftJoin('permissions as c', 'c.id', '=', 's_menu_sub.permission_id')
			->orderBy('s_menu_sub.id', 'desc')
			->where('s_menu_sub.id', '=', $id)
			->first();

		return is_object($rows) ? $rows->toArray() : [];
	}

	public function getMenu() {
		$m = $this->all();
		if (is_object($m)) {
			return $m->toArray();
		}
		return [];
	}

	public function createMenu($menuId, $permissionId, $controller, $apiLink = '', $desc = '', $isEnable = 0) {
		$menu = $this->where('api_link', '=', $apiLink)->first();
		if (is_object($menu)) {
			return $menu;
		} else {
			return $this->create([
				'menu_id' => $menuId,
				'permission_id' => $permissionId,
				'controller' => $controller,
				'api_link' => $apiLink,
				'is_enable' => $isEnable,
				'description' => $desc,
			]);
		}
	}

	public function updateMenu($id, $menuId, $permissionId, $controller, $apiLink = '', $desc = '', $isEnable = 0) {
		return $this->where('id', '=', $id)->update([
			'menu_id' => $menuId,
			'permission_id' => $permissionId,
			'controller' => $controller,
			'api_link' => $apiLink,
			'is_enable' => $isEnable,
			'description' => $desc,
		]);
	}

	// 删除
	public function del($id) {
		return $this->where('id', '=', $id)->delete();
	}

}
