<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \App\Teacher;

use App\Http\Model\Roles;

class IndexController extends Controller
{
    //http://manager.demo.com/test
    public function index() {
        // $pdo = DB::connection()->getPdo();
        // dd($pdo);

         $table_role = new Roles();
         $row = $table_role->getRowById(1);

         print_r($row);

        // $hasRole = $table_role->hasRole('Admin1');
        // var_dump($hasRole);

        var_dump("index");exit;
        // $t = \App\Teacher::all()->toArray();
        // print_r($t);


    }
}
