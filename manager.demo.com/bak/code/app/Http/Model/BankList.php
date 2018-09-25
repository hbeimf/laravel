<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class BankList extends Model {

	protected $table = 'bw_bank_list';

	public $timestamps = true;

    protected $fillable = [
        'name'
    ];
}
