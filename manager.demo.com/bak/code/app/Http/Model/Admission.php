<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Admission extends Model {
    const STATUS3_YES = 1;  //启用
    const STATUS3_NO = 2;  //停用


    protected  $table='bw_admission';

    protected $fillable = [
        'type',
        'bank',
        'number',
        'user_name',
        'bank_name',
        'url',
        'province',
        'city',
        'sort',
        'group',
        'status',
    ];


    public function store(Request $request){
        $validatedData = $request->validate([
            'number' => 'required|unique:posts|max:0',
            'body' => 'required',
        ]);
    }

}