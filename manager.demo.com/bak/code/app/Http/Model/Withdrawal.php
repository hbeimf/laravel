<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model {
    const STATUS_NO = 1; //未处理
    const STATUS_YES = 2; //已处理
    const STATUS_REFUSE = 3;//拒绝

	protected $table = 'lo_withdrawal';

	public $timestamps = false;

}
