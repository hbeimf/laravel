<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    //http://manager.demo.com/test
    public function index() {
        $pdo = DB::connection()->getPdo();
        dd($pdo);


    }
}
