<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \App\Teacher;

class IndexController extends Controller
{
    //http://manager.demo.com/test
    public function index() {
        // $pdo = DB::connection()->getPdo();
        // dd($pdo);

    	var_dump("index");exit;
        // $t = \App\Teacher::all()->toArray();
        // print_r($t);


    }
}
