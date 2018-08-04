<?php
// https://www.jianshu.com/p/2988ba405b3b?from=timeline&isappinstalled=0

// CREATE TABLE `users` (
//   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//   `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `created_at` timestamp NULL DEFAULT NULL,
//   `updated_at` timestamp NULL DEFAULT NULL,
//   PRIMARY KEY (`id`),
//   UNIQUE KEY `users_email_unique` (`email`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// class UserController extends Controller
// {
//     //
// }


use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}

