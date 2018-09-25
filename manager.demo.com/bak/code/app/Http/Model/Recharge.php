<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class Recharge extends Model {
    const CHANNEL_ALIPAY = 1; //支付宝
    const CHANNEL_WECHAT = 2;  //微信
    const CHANNEL_OFFLINE = 3;  //人工线下

    const STATUS_UNPAY = 1; //未支付
    const STATUS_PAYED = 2; //已支付
    const STATUS_TIMEOUT = 3; //已超时
    const STATUS_REFUND = 4;//已退款
    const STATUS_PENDING = 9;//待审核

	protected $table = 'lo_recharge';

	public $timestamps = false;

}
