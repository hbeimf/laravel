<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class SystemCeilPoint extends Model {
	
	protected $table = 'bw_system_ceil_point';

	public $timestamps = true;

    const STATUS_YES = 1; //启用
    const STATUS_NO = 2; //禁用


}
