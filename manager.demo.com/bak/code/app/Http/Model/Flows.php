<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Flows extends Model {
	protected $table = 'lo_flows';

	public $timestamps = false;

	public function getListByUid($uid, $perPage) {
        return $this->where('uid', $uid)->paginate($perPage);
    }

}
