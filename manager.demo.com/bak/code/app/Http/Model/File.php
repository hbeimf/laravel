<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class File extends Model {
	protected $table = 'bw_file';

	public $timestamps = false;
	
	
	public function checkHttps() {
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') 
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) 
			&& $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
		return $http_type;
	}
	
	public function getUrl() {
		return "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/{$this->dirType}/{$this->name}";
	}
}
