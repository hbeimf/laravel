<?php

namespace App\Http\Controllers\Admin\V1;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \Logger;

class Controller extends BaseController {

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

	public $errorStatus = 403;

	public function __construct() {
		$config = config('app.log_config_db');
		Logger::configure($config);
		$this->log = Logger::getLogger(__CLASS__);
	}
}
