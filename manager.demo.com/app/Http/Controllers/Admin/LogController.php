<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
// use \Logger; 

// https://packagist.org/packages/apache/log4php
// composer require apache/log4php
// http://logging.apache.org/log4php/
// https://blog.csdn.net/ty_hf/article/details/51129495
class LogController extends CommonController
{

	// public function __construct(){
	// 	echo 'http://la.demo.com/admin/log';	
	// 	$config = config('app.log_config_file');
	//     	Logger::configure($config);
	// 	$this->log = Logger::getLogger(__CLASS__);
	// }
    //http://la.demo.com/admin/log
    // 将日志写到文件里
    public function index() {
    	echo 'http://la.demo.com/admin/log';
    	// $this->log_file();
    	$this->log_file_v2();
    	// $this->log_db();
    }

 //    private function log_file() {
 //    	// http://www.php.cn/php-weizijiaocheng-375904.html
 //    	Logger::configure($_SERVER['DOCUMENT_ROOT']."/../config/appender_file.properties");
	// $logger = Logger::getRootLogger();
	// $logger->debug(" ====== Log Hello World!");
 //    }


    // https://blog.csdn.net/ty_hf/article/details/51129495
    private function log_file_v2() {
 //    	$config = config('app.log_config_file');
 //    	Logger::configure($config);
	// $this->log = Logger::getLogger(__CLASS__);

	$this->log->debug("My second message.222"); // Not logged because DEBUG < WARN
	$this->log->info("My third message.33"); // Not logged because INFO < WARN
	$this->log->warn("My fourth message444444"); // Logged because WARN >= WARN
	$this->log->error("My fifth message5555"); // Logged because ERROR >= WARN
	$this->log->fatal("My sixth message66"); // Logged because FATAL >= WARN

    }


 //    private function log_db() {
 //    	$config = config('app.log_config_db');
 //    	Logger::configure($config);
	// $this->log = Logger::getLogger(__CLASS__);

	// $this->log->debug("My second message.111<br>"); // Not logged because DEBUG < WARN
	// $this->log->info("My third message.1111<br>"); // Not logged because INFO < WARN
	// $this->log->warn("My fourth message.111<br>"); // Logged because WARN >= WARN
	// $this->log->error("My fifth message.111<br>"); // Logged because ERROR >= WARN
	// $this->log->fatal("My sixth message.1111<br>\r\n<------------------>"); // Logged because FATAL >= WARN
    	
 //    }



}
