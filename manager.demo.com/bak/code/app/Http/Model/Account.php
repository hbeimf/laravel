<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Account extends Model {
	protected $table = 'lo_account';
    protected $primaryKey = 'uid';

	public $timestamps = false;

	public function getAccountByUid($uid, $columns = ['*']) {
        return $this->where('uid', $uid)->first($columns);
    }

}
