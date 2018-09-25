<?php

namespace App\Repository;


use App\Validator\InmoneyValidator;
use App\Http\Model\DepositConfig;
use App\Http\Model\MoneyLog;
use App\Http\Model\UserRelevantInfo;
use App\Http\Model\Audit;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;

class InmoneyRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'status' => '=',
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return InmoneyValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\Inmoney::class;
    }
	
	/**
	 * 人工存入
	 * @param type $params 
	 * @param type $aid
	 * @return type
	 */
	public function addInmoney($params, $aid) {
		if ( ! in_array($params['in_type'], ['人工存入', '负数额度归零', '活动优惠', '返点优惠', '人工存款'])) {
			return ['msg' => '存入类型错误'];
		}
		
		$this->model->uid = $params['uid'];
		$this->model->money = $params['money'];
		$this->model->in_type = $params['in_type'];
		$params['in_model'] = $params['in_type'] == '人工存入' ? 1 : 0;
		$this->model->in_model = $params['in_model'];
		isset($params['remit_remark']) && $this->model->remit_remark = $params['remit_remark']; // 汇款优惠备注【额外优惠】
		isset($params['deposit_remark']) && $this->model->deposit_remark = $params['deposit_remark']; // 存款优惠备注【优惠】
		isset($params['remark']) && $this->model->remark = $params['remark']; // 存入金额备注
		isset($params['synthetical_audit_set']) && $this->model->synthetical_audit_set = $params['synthetical_audit_set']; // 是否设置综合稽核
		isset($params['synthetical_audit']) && $params['synthetical_audit_set'] == 1 && $this->model->synthetical_audit = $params['synthetical_audit']; // 综合稽核打码量
		isset($params['in_type']) && $this->model->in_type = $params['in_type']; // 存入模式名称
		$this->model->pay_scheme = 'MR'; // 人工存款
		$this->model->manage_id = $aid; // 管理员id

		$depositModel = new DepositConfig();
		$deposit = $depositModel->where('id', $params['in_model'])->first();
		if ($params['in_model'] == 1 && ($params['money'] > $deposit->max_money || $params['money'] < $deposit->min_money)) {
			return ['msg' => '金额不在最大最小限制范围内'];
		}
		// [存款优惠]
		if ($params['in_model'] == 1 && isset($params['remit_money'])) {
			$this->model->remit_money = $params['remit_money'];
		}
		// [汇款优惠]
		if ($params['in_model'] == 1 && isset($params['deposit_money'])) {
			$this->model->deposit_money = $params['deposit_money'];
		}
		
		$this->_audit($params, $deposit); // 常态稽核和服务费
		
		DB::beginTransaction();
		try {
			$this->model->save(); // inmoney表存储
			$moneyLogModel = new MoneyLog();
			$UserRelevantModel = new UserRelevantInfo();
			$auditModel = new Audit();
			$this->_moneyLog($moneyLogModel); // 流水日志
			$this->_updateUserRelevant($UserRelevantModel); // 用户余额更新
			$this->_auditLog($auditModel); // 打码表添加记录
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();//事务回滚
			$msg = $e->getMessage();
			return ['msg' => $msg];
		}
		
		return ['success' => true];
	}
	
	/**
	 * 优惠和额外优惠
	 * @param type $prefix
	 * @param type $inModel
	 */
	public function discount($inModel, $num, $money, $prefix='') {
		$retMoney = 0;
		$discountType = $prefix.'discount_type';
		$discountTime = $prefix.'discount_time';
		$discountMoney = $prefix.'discount_money';
		$discountMaxMoney = $prefix.'discount_max_money';
		$discountProportion = $prefix.'discount_proportion';
		if ($inModel->$discountType > 0) {
			$getDiscount = false;
			switch ($inModel->$discountType) {
				case 1: // 第一次
					$num == 0 && $getDiscount = true;
					break;
				case 2:	// 每次
					$getDiscount = true;
					break;
				case 3:
					$num < $inModel->$discountTime && $getDiscount = true;
					break;
			}
			if ($getDiscount && $money >= $inModel->$discountMoney) {
				$retMoney = round($money * $inModel->$discountProportion/100, 2);
				$retMoney = $retMoney > $inModel->$discountMaxMoney ? $inModel->$discountMaxMoney : $retMoney;
			}
			return $retMoney;
		}
		return 0;
	}
	
	/**
	 * 计算打码量和服务费
	 * @param type $params
	 */
	private function _audit ($params, $deposit) {
		if ($params['in_model'] == 1) { // 人工存入
			if (isset($params['audit_set']) && $params['audit_set'] == 1) { // 需要稽核打码
				list($this->model->audit, $this->model->server_fee) = $this->normalAudit($params['money'], $deposit);
			} else {
				$this->model->audit = 0;
				$this->model->server_fee = 0;
			}
		} elseif ($params['in_model'] == 0) { //负数额度归零、活动优惠、返点优惠、人工存款
			$this->model->audit = 0;
			$this->model->server_fee = 0;
		}
	}
	
	/**
	 * 常态打码计算
	 */
	public function normalAudit ($money, $deposit) {
		$audit = $money * $deposit->proportion/100 - $deposit->relaxable;
		$fee = $money * $deposit->administrative_rate/100;
		return [$audit, $fee];
	}
	
	/**
	 * 记录流水
	 */
	private function _moneyLog($model) {
		// 存款金额
		if ($this->model->money > 0) {
			$this->_doMoneyLog(4, '入账' . $this->model->money, $model, $this->model->money, $model);
		}
		// 存款优惠
		if (isset($this->model->remit_money) && $this->model->remit_money > 0) {
			$this->_doMoneyLog(2, '存款优惠' . $this->model->remit_money, $model, $this->model->remit_money, $model);
		}
		// 汇款优惠
		if (isset($this->model->deposit_money) && $this->model->deposit_money > 0) {
			$this->_doMoneyLog(3, '汇款优惠' . $this->model->deposit_money, $model, $this->model->deposit_money, $model);
		}
	}
	
	/**
	 * 
	 * @param type $typeId 存入消息类型
	 * @param type $msg 消息内容
	 * @param type $model 流水model
	 * @param type $money 记录的金额
	 * @param type $inOrOut 进或出款
	 */
	private function _doMoneyLog($typeId, $msg, $model, $money, $inOrOut=2) {
		$data = [
			'uid' => $this->model->uid,
			'manage_id' => $this->model->manage_id, 
			'in_out' => $inOrOut, 
			'msg' => $this->model->in_type . ' | ' . $msg, 
			'created_at' => date('Y-m-d H:i:s'), 
			'type_id' => $typeId, 
			'money' => $money, 
		];
		$model->insert($data);
	}

	
	/**
	 * 更新用余额
	 * @param type $model 用户相关信息model
	 */
	private function _updateUserRelevant($model) {
		$exist = $model->where('uid', $this->model->uid)->first();
		if (empty($exist)) {
			$model->uid = $this->model->uid;
			$model->recharge_all = $this->model->money;
			$model->total_money = $this->model->money;
			$model->created_at = date('Y-m-d H:i:s');
			$model->updated_at = date('Y-m-d H:i:s');
			$model->save();
		} else {
			$update = [
				'recharge_all' => $exist['recharge_all'] + $this->model->money,// 充值总额
				'total_money' => $exist['total_money'] + $this->model->money,// 余额
				'updated_at' => date('Y-m-d H:i:s')
			];
			$model->updateUser($update, $this->model->uid);
		}
	}
	
	/**
	 * 用户存款次数
	 * @param type $uid
	 * @return type
	 */
	public function inmoneyCount($uid) {
		return $this->model->where('uid', $uid)->count();
	}

	/**
	 * 记录稽核记录
	 * @param type $model
	 */
	public function _auditLog($model) {
		$model->uid = $this->model->uid;
		$model->status = 0;
		$model->in_id = $this->model->id;
		$model->created_at = date('Y-m-d H:i:s');
		$model->updated_at = date('Y-m-d H:i:s');
		$model->save();
	}
}