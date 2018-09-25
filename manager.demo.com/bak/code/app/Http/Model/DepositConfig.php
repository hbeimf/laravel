<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class DepositConfig extends Model
{

	protected $table ='bw_deposit_config';

    protected $fillable = [
		'id', 'name', 'max_money', 'min_money', 'discount_type', 'discount_time',
		'discount_giveup', 'discount_money', 'discount_proportion', 'discount_max_money',
		'ex_discount_type', 'ex_discount_time', 'ex_discount_giveup', 'ex_discount_money',
		'ex_discount_proportion', 'is_enable', 'proportion', 'relaxable', 
		'administrative_rate', 'ex_is_enable', 'code_checking', 'note', 'created_at',
		'updated_at'
    ];
}
