<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Test extends Model {

	protected $table = 'users';

	protected $fillable = [
		'name', 'email', 'password',
	];
}
