<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{

    protected $table ='bw_test';

    protected $fillable = [
        'user_name','bank_name','bank_number'
    ];
}
