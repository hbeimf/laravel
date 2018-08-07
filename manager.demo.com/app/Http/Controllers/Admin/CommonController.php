<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Logger; 

class CommonController extends Controller
{
    //

    public function __construct(){
		echo 'http://la.demo.com/admin/log';	
		$config = config('app.log_config_file');
	    	Logger::configure($config);
		$this->log = Logger::getLogger(__CLASS__);
	}
}
