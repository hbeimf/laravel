<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Domain extends Model {
	protected $table = 'bw_domain';

	public $timestamps = true;

    const STATUS_YES = 1; //启用
    const STATUS_NO = 2; //禁用
	
	
	
}
