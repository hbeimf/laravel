<?php
namespace App\Http\Model;

use App\User;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\DB;

class AdminInfo extends Model {
	protected $table = 's_admin_info';

	// public $timestamps = false;

	protected $fillable = ['uid', 'status', 'nickname', 'img_id']; //开启白名单字段


    public  function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

	public function getRoleByUid($uid) {
		$sql = "select a.slug, a.name, b.user_id from roles as a left join role_user as b on a.id = b.role_id where b.user_id = " . $uid;
		$obj = DB::select($sql);
		if (isset($obj[0])) {
			return $obj[0]->slug;
		}
		return '';
	}

	public function existsUserByUid($uid, $name = '') {
		$obj = $this->where('name', '=', $name)->where('uid', '!=', $uid)->first();
		return is_object($obj);
	}

	public function getRowById($id) {
		return $this->where('id', $id)->first()->toArray();
	}

	public function getRowByUid($uid) {
		return $this->where('uid', $uid)->first()->toArray();
	}

	// 获取管理员的昵称与头像
	public function getAdminInfo($uid) {
		$reply = [
			'uid' => $uid,
			'nickname' => '无昵称',
			'image' => 'https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/s%3D220/sign=13075a6735fae6cd08b4ac633fb30f9e/4bed2e738bd4b31c032d2feb87d6277f9e2ff849.jpg',
		];
		$obj = $this->where('uid', '=', $uid)->first();
		if (is_object($obj)) {
			if ($obj->nickname != '') {
				$reply['nickname'] = $obj->nickname;
			}
		}
		return $reply;
	}

	public function getAdminByUid($uid) {
		$domain = self::getDomain();

		$select = "s_admin_info.id,
			s_admin_info.uid,
			s_admin_info.status,
			s_admin_info.nickname,
			s_admin_info.img_id,
			concat('{$domain}/', c.dirType, '/', c.name)  as img_url,
			b.name, b.email, b.created_at, b.updated_at,
			e.id as role_id, e.name as role_name";

		$table = $this->selectRaw($select);
		$rows = $table
			->leftJoin('users as b', 'b.id', '=', 's_admin_info.uid')
			->leftJoin('bw_file as c', 'c.id', '=', 's_admin_info.img_id')
			->leftJoin('role_user as d', 'd.user_id', '=', 's_admin_info.uid')
			->leftJoin('roles as e', 'e.id', '=', 'd.role_id')
			->where('b.id', '=', $uid)
			->first();
		if (is_object($rows)) {
			return $rows->toArray();
		}
		return [];
	}

	public function createAdmin($uid, $nickname = '', $img_id = 0, $status = 1) {
		if (trim($nickname) == '') {
			$nickname = '';
		}
		$obj = $this->where('uid', '=', $uid)->first();
		if (is_object($obj)) {
			return $obj;
		} else {
			return $this->create([
				'uid' => $uid,
				'nickname' => $nickname,
				'img_id' => $img_id,
				'status' => $status,
			]);
		}
	}

	// 更新管理员昵称与头像
	public function updateAdminInfo($uid, $nickname = '', $img_id = 0, $status = 1) {
		if (trim($nickname) == '') {
			$nickname = '';
		}
		return $this->where('uid', '=', $uid)->update([
			'nickname' => $nickname,
			'img_id' => $img_id,
			'status' => $status,
		]);
	}

	public function updateStatus($uid, $status) {
		return $this->where('uid', '=', $uid)->update([
			'status' => $status,
		]);
	}

	public static function getDomain() {
		return 'http://' . $_SERVER['HTTP_HOST'] . '/';
	}
}
