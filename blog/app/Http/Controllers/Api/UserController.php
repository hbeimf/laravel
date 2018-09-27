<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller {
	public $successStatus = 200;

	public function __construct() {
		$this->content = array();
	}
	public function login() {
		// dd(request('name'));
		if (Auth::attempt(['name' => request('name'), 'password' => request('password')])) {
			$user = Auth::user();
			$this->content['token'] = $user->createToken('Pi App')->accessToken;
			$status = 200;
		} else {

			$this->content['error'] = "未授权";
			$status = 401;
		}
		return response()->json($this->content, $status);
	}
	public function passport() {
		return response()->json(['user' => Auth::user()]);
	}

	public function register(Request $request) {
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'password' => 'required',
			'c_password' => 'required|same:password',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()], 401);
		}

		$input = $request->all();
		$input['password'] = bcrypt($input['password']);
		$user = User::create($input);
		$success['token'] = $user->createToken('MyApp')->accessToken;
		$success['name'] = $user->name;

		return response()->json(['success' => $success], $this->successStatus);
	}

	/**
	 * details api
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getDetails() {
		$user = Auth::user();
		return response()->json(['success' => $user], $this->successStatus);
	}
}