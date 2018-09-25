<?php
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

class BankCard extends Model {
    const STATUS_YES = 1;   //启用
    const STATUS_NO = 2;    //禁用

    const ISDEFAULT_YES = 1;   //默认卡
    const ISDEFAULT_NO = 2;    //非默认卡

	protected $table = 'bw_bank_card';

	protected $fillable = [
	    'uid','bank_name','card_num','bank_area','bank_branch','user_name','status','is_default'
    ];

    //protected $guarded = ['id'];



	public $timestamps = true;

	public function getCardById($id, $columns = ['*']) {
        return $this->where(['id' => $id, 'status' => self::STATUS_YES])->first($columns);
    }
	
	public function getDefault($uid) {
		return $this->where(['uid'=>$uid,'status' => self::STATUS_YES])->orderBy("is_default","asc")->first(['bank_name','card_num']);
	}

}
