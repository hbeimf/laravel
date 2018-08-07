<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \App\Teacher;

use \Logger;

class IndexController extends Controller
{
    //http://la.demo.com/test
    public function index() {
        // $pdo = DB::connection()->getPdo();
        // dd($pdo);

    	// var_dump("index");exit;
        // $t = \App\Teacher::all()->toArray();
        // print_r($t);

    	// http://www.php.cn/php-weizijiaocheng-375904.html
    	// echo dirname(__FILE__).'/../resources/appender_file.properties';exit;
    	// print_r($_SERVER['DOCUMENT_ROOT']."/../config/appender_file.properties");
    	Logger::configure($_SERVER['DOCUMENT_ROOT']."/../config/appender_file.properties");
	
	// Logger::configure(dirname(__FILE__).'/../resources/appender_file.properties');
	$logger = Logger::getRootLogger();
	$logger->debug("Hello World!");

    }
}
