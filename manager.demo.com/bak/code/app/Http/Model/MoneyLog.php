<?php
namespace App\Http\Model;

use App\User;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\DB;

class MoneyLog extends Model {
	protected $table = 'bw_money_log';

	const TYPE_RECHARGE = 1;    //充值
	const TYPE_RECHARGE_DISCOUNTS = 2;  //充值优惠【存款优惠】
	const TYPE_RECHARGE_DISCOUNTS_EXTRA = 3;  //额外优惠【汇款优惠】
	const TYPE_RECHARGE_DISCOUNTS_ARTIFICIAL = 4;  //人工存入
	const TYPE_WITHDRAWAL = 5;  //提现扣款
	const TYPE_WITHDRAWAL_MONEY = 6;  //人工提出

	public $timestamps = true;

    protected $fillable = ['uid', 'manage_id', 'in_out', 'msg', 'create_at', 'type_id', 'money', 'balance']; //开启白名单字段

    public function add($data) {
        if(empty($data['uid']) || empty($money) || empty($data['type_id'])) {
            return false;
        }
        $this->insert($data);

    }

}
