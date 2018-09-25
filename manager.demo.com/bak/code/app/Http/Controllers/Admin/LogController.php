<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use \Logger; 

class LogController extends CommonController
{

    //http://code.demo.com/admin/log
    // 将日志写到文件里
    public function index() {
    	$this->log->debug("My second message.222"); // Not logged because DEBUG < WARN
	$this->log->info("My third message.33"); // Not logged because INFO < WARN
	$this->log->warn("My fourth message444444"); // Logged because WARN >= WARN
	$this->log->error("My fifth message5555"); // Logged because ERROR >= WARN
	$this->log->fatal("My sixth message66"); // Logged because FATAL >= WARN
    }

}
