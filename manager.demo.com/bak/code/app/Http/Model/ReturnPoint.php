<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class ReturnPoint extends Model {
	protected $table = 'bw_return_point';

	public $timestamps = false;
	
	protected $fillable = ['uid', 'promote_id', 'game_id', 'point', 'addtime']; //开启白名单字段

	public function getPointByPromoteId($promoteIsd, $columns = ['*']) {
	    return $this->where('promote_id', $promoteIsd)->get($columns);
	}

	public function updateUser($update, $id) {
		return $this->where('id', '=', $id)->update($update);
	}
}
