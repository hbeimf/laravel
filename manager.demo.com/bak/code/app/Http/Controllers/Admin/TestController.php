<?php
namespace App\Http\Controllers\Admin;

use App\Http\Model\Menu;
use App\Http\Model\Menusub;
use App\Http\Model\Permissions;
use App\Http\Model\PromoteConfig;
use App\Http\Model\Roles;
use App\User;
use Illuminate\Http\Request;

// use App\Http\Model\Menusub;

class TestController extends CommonController {

	public function index() {
		// $this->testPermission();
		// $this->testMenu();

		// $this->testRouters();
		// $this->testGetCurrentRouter();

		// $this->testEnv();

		// $this->testAttachRoleAndPermission();
		// $this->testRegisterAdmin();
		// $this->testGetAdmin();
		// $this->testCreateMenuInner();


		// $this->testLogin();

		$this->testMenuSub();
		// $this->testDomain();

	}

	public function testMenuSub() {
		$t = new Menusub();
		$id = 2;

		$r = $t->getMenuById($id);

		print_r($r->toArray());

	}

	// public function testRole

	public function testDomain() {
		print_r('http://' . $_SERVER['HTTP_HOST'] . '/');
	}

	public function testRole() {
		$t = new Roles();
		// $uid = 6;
		// $role = $t->getRoleByUid($uid);
		$role = $t->getRoleById(13);
		var_dump($role);
	}

	// http://code.demo.com/api/test?email=11122233344@qq.com&password=1234567
	// http://code.demo.com/api/test?name=admin1&password=1234567
	public function testLogin() {
		// $r = Auth::attempt(['email' => request('email'), 'password' => request('password')]);
		// $r = Auth::attempt(['name' => request('name'), 'password' => request('password')]);

		// var_dump($r);
	}

	public function testCreateMenuInner() {
		$params = [
			'id' => 1,
			'menu_id' => 2,
			'permission_id' => 1,
			'controller' => 'ctrl1up',
			'api_link' => '/api/createMenu',
			'description' => '测试update',
		];

		$t = new Menusub();
		$r = $t->updateMenu($params['id'], $params['menu_id'], $params['permission_id'], $params['controller'], $params['api_link'], $params['description']);

		var_dump($r);

	}

	public function testGetAdmin() {
		$t = new Roles();
		$admin = $t->getAdmin();
		print_r($admin->id);
	}

	// [name] => admin
	//    [email] => 111222@qq.com
	//    [password] => $2y$10$AP72vKVNUpve1llE68r0rO5lzhID07AtlIHQq793xMDTCWiksQdUa
	//    [c_password] => 123456
	//    [_url] => /api/adminRegister/

	public function testRegisterAdmin() {
		$admin = [
			'name' => 'admin',
			'email' => '111222333@qq.com',
			'password' => '$2y$10$AP72vKVNUpve1llE68r0rO5lzhID07AtlIHQq793xMDTCWiksQdUa',
			'c_password' => '123456',
			'_url' => '/api/adminRegister/',
		];

		$r = User::selectRaw('id')->where('name', '=', $admin['name'])->orWhere('email', '=', $admin['email'])->first();

		if (is_object($r)) {
			var_dump("error");
		} else {
			$user = User::create($admin);
			$success['token'] = $user->createToken('MyApp')->accessToken;
			$success['name'] = $user->name;

			print_r($success);
		}

	}

	public function testAttachRoleAndPermission() {
		$t = new Permissions();
		$t->attachPermissionAndRole(12, 5);

	}

	public function testEnv() {
		echo getenv('DB_DATABASE');

	}
	public function testGetCurrentRouter() {
		echo \Request::getRequestUri();
	}

	// php artisan route:list
	public function testRouters() {
		$app = app();
		$routes = $app->routes->getRoutes();
		$path = [];
		foreach ($routes as $k => $value) {
			$path[$k]['uri'] = $value->uri;
			$path[$k]['path'] = $value->methods[0];
		}
		print_r($path);
	}

	public function testMenu() {
		// $this->createMenu();
		// $this->updateMenu();
		$this->getMenu();
	}

	private function getMenu() {
		$t = new Menu();
		$menus = $mm = $t->getMenu();

		$theMenu = [];
		foreach ($menus as $m) {
			if ($m['parent_id'] == 0) {
				$theMenu[] = $this->getChildMenu($m, $mm);
			}
		}

		echo json_encode(['code' => $this->successStatus,
			'success' => $theMenu]);exit;

	}

	private function getChildMenu($menu, $menus) {
		$menu['children'] = [];
		foreach ($menus as $m1) {
			if ($m1['parent_id'] == $menu['id']) {
				$menu['children'][] = $m1;
			}
		}
		return $menu;
	}

	private function updateMenu() {

		$t = new Menu();
		$id = 1;
		$name = '测试update';
		$permissionId = 1;
		$apiLink = '/api/getMenu';
		$parentId = 0;
		$isEnable = 0;
		$desc = '测试增加updte';
		$r = $t->updateMenu($id, $name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);

	}

	private function createMenu() {
		$this->createMenu1();
		$this->createMenu2();
		$this->createMenu3();
		$this->createMenu4();
		$this->createMenu5();

	}

	private function createMenu1() {
		$t = new Menu();
		$name = '测试';
		$permissionId = 1;
		$apiLink = '/api/getMenu';
		$parentId = 0;
		$isEnable = 0;
		$desc = '测试增加';
		$r = $t->createMenu($name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);
	}

	private function createMenu2() {
		$t = new Menu();
		$name = '测试222';
		$permissionId = 1;
		$apiLink = '/api/getPermission';
		$parentId = 1;
		$isEnable = 0;
		$desc = '测试增加';
		$r = $t->createMenu($name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);
	}

	private function createMenu3() {
		$t = new Menu();
		$name = '测试333';
		$permissionId = 1;
		$apiLink = '/api/getPermission333';
		$parentId = 1;
		$isEnable = 0;
		$desc = '测试增加';
		$r = $t->createMenu($name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);
	}

	private function createMenu4() {
		$t = new Menu();
		$name = '测试444';
		$permissionId = 1;
		$apiLink = '/api/getPermission444';
		$parentId = 0;
		$isEnable = 0;
		$desc = '测试增加';
		$r = $t->createMenu($name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);
	}

	private function createMenu5() {
		$t = new Menu();
		$name = '测试5555';
		$permissionId = 1;
		$apiLink = '/api/getPermission5555';
		$parentId = 4;
		$isEnable = 0;
		$desc = '测试增加';
		$r = $t->createMenu($name, $permissionId, $apiLink, $parentId, $isEnable, $desc);

		print_r($r);
	}

	public function testPermission() {
		// init data
		$this->createPermission();
		// $this->attachPermission();
		// $this->attachRole();

		// // // check
		// $p = new Permissions();
		// $r = $p->hasPermission($this->userId, $this->permissionId);

		// var_dump($r);

		// $this->createRole();
	}

	private function createRole() {
		$table_role = new Roles();
		$name = "ATest";
		$slug = "atest";
		$role = $table_role->createRole($name, $slug, 'a test', 1);

	}

	private function attachRole() {
		$t = new Roles();
		$t->attachRole($this->userId, $this->roleId);

	}

	private function attachPermission() {

		$p = new Permissions();
		$p->attachPermission($this->permissionId, $this->roleId);
	}

	private function createPermission() {
		$p = new Permissions();
		$pp = $p->createPermission();
		print_r($pp->toArray());
	}


}