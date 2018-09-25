<?php
namespace App\Http\Model;

// use Illuminate\Database\Capsule\Manager as DB;
use \Illuminate\Database\Eloquent\Model;

class Menu extends Model {
	protected $table = 's_menu';

	// public $timestamps = false;

	protected $fillable = ['parent_id', 'permission_id', 'name', 'api_link', 'url', 'icon', 'is_enable', 'description', 'order_by', 'is_show']; //开启白名单字段

	public function getRowById($id) {
		return $this->where('id', $id)->first()->toArray();
	}

	public function getRowByParentId($id) {
		$select = 'id, parent_id, permission_id, name, api_link, url, icon, is_enable, description as `desc`, created_at, updated_at, order_by, is_show';
		$r = $this->selectRaw($select)->where('parent_id', '=', $id)->orderBy('order_by', 'asc')->get();
		return (is_object($r)) ? $r->toArray() : [];
	}

	public function getMenu() {
		$m = $this->all();
		if (is_object($m)) {
			return $m->toArray();
		}
		return [];
	}

	public function createMenu($name, $permissionId, $apiLink = '', $url = '', $icon = '', $parentId = 0, $isEnable = 0, $desc = '', $orderBy = 0, $isShow = 0) {
		$menu = $this->where('api_link', '=', $apiLink)->first();
		// if (is_object($menu)) {
		// 	return $menu;
		// } else {
		return $this->create([
			'parent_id' => $parentId,
			'permission_id' => $permissionId,
			'name' => $name,
			'api_link' => $apiLink,
			'url' => $url,
			'icon' => $icon,
			'is_enable' => $isEnable,
			'description' => $desc,
			'order_by' => $orderBy,
			'is_show' => $isShow,
		]);
		// }
	}

	public function updateMenu($id, $name, $permissionId, $apiLink = '', $url = '', $icon = '', $parentId = 0, $isEnable = 0, $desc = '', $orderBy, $isShow = 0) {
		return $this->where('id', '=', $id)->update([
			'parent_id' => $parentId,
			'permission_id' => $permissionId,
			'name' => $name,
			'api_link' => $apiLink,
			'url' => $url,
			'icon' => $icon,
			'is_enable' => $isEnable,
			'description' => $desc,
			'order_by' => $orderBy,
			'is_show' => $isShow,
		]);
	}

	// 删除
	public function del($id) {
		return $this->where('id', '=', $id)->delete();
	}

}
