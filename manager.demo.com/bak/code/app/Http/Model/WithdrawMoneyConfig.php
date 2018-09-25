<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

// 取款模式
class WithdrawMoneyConfig extends Model {

	protected $table = 'bw_withdraw_money_config';

	protected $fillable = [
		'name', 'free_time', 'service_charge', 'withdraw_money', 'is_need_wait',
		'wait_money', 'wait_hour', 'max_money', 'min_money', 'note',
	];
}
