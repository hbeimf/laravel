<?php

namespace App\Http\Model;

use App\Http\Model\Users;
use Illuminate\Database\Eloquent\Model;

use App\Http\Model\WithdrawMoneyConfig;
use Illuminate\Support\Facades\DB;

class OutMoney extends Model
{

    /**
     * @var array 人工提出类型, 数据表的字段类型为ENUM
     */
    static $withdrawType = [
        '重复出款',
        '公司入款失误',
        '会员负数回冲',
        '手动申请出款',
        '扣除非法下注派彩',
        '放弃存款优惠',
        '误存提出',
        '其他'
    ];

    static $payScheme = [
      'MT', // 人工提出
      'MC'  // 线下提出
    ];

    protected $table = 'bw_outmoney';
	public $messages = [];
	public $money = 0;

	protected $appends = [
        'withdraw_number'
    ];
	
	protected $fillable = [
	    'uid', 'withdraw_config_id', 'manage_id', 'pay_scheme', 'withdraw_type',
	    'withdraw_money', 'withdraw_money_actual', 'de_discount', 'de_audit', 'de_service_charge',
	    'is_first', 'discount_removed', 'bank_name', 'remark', 'lock_uid', 'lock_at', 'status',
	    'audit_ids'
	];

    public function getWithdrawNumberAttribute()
    {
        return $this->pay_scheme.sprintf("%09d", $this->id);
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'uid');
    }
	
	public function WithdrawMoneyConfig(){
		return $this->belongsTo('App\Http\Model\WithdrawMoneyConfig', 'withdraw_config_id');
	}
	
	/**
	 * @todo 锁定这条记录
	 * @param int $uid 管理员的UID
	 * **/
	public function toLock($uid) {
		if($uid <= 0) return false;  //没有提交 uid,不能锁定
		if($this->isLock($uid)){
			$this->lock_uid = $uid;
			$this->lock_at = date('Y-m-d H:i:s');
			return $this->save();
		}
		return false;
	}
	
	/**
	 * @todo 锁定用户判断
	 * @param int $uid 申请判断锁定的工作人员uid
	 * @return bool 没有锁定返回true,锁定返回false,没有提交uid返回false
	 * **/
	public function isLock($uid = 0){
		if($uid <=0)			return false;
		if($this->lock_uid != $uid && time() - strtotime($this->lock_at) <=600){
			return false;
		}
		return true;
	}
	
	
	/**
	 * @todo 备注
	 * @param int $uid 备注人的uid
	 * @param string $remark 备注的内容
	 * **/
	public function toRemark($uid,$remark) {
		if($uid<=0 || empty($remark)){
			return false;
		}
		
		$user = Users::find($uid);
		if(empty($user)){
			return false;
		}
		
		$remark = $user->name.":".$remark."\n";
		
		$this->remark = $this->remark.$remark;
		return $this->save();
	}
	
	/**
	 * @todo 审核通过
	 * @param int $uid 操作人的uid
	 * **/
	public function confirm($uid=0) {
		//确认订单状态
		$this->status = 1;
		$back = $this->save();
		
		if($back){
			//添加稽核表
			$Audits = new Audit();
			$Audits->uid = $this->uid;
			$Audits->out_id = $this->id;
			$back = $this->save();
			
			
			
			return true;
		}
		return false;
	}
	
	
	/**
	 * @todo 计算当前用户提现需要交的手续费
	 *		1.获得out_model的配置,
	 *		2.匹配需要提现的钱是否符合要求
	 * @param int $uid 需要被计算的用户
	 * @param int $out_model 取款模式,估计取款模式了
	 * @return array 具体出款详情
	 *				[
	 *					'list' => [],		//参与稽核的条目的详情
	 *					'total_money' => 0,    //用户账户资金
	 *					'money' => 0 ,   //用户本次稽核后能提现的费用,已经减去下面 三项 费用
	 *					'de_audit' => 0,      //收取行政服务费,未达标收取
	 *					'de_discoun' => 0,  //扣除之前给的优惠,未达标收取
	 *					'de_overtime_charge' => 0    //收取手续费,超过取现次数收取
	 *				];
	 * **/
	public function audit($out_model=0,$uid=0){
		if(empty($out_model)||empty($uid)){
			return false; //参数不足不能进行
		}
		
		$model = WithdrawMoneyConfig::find($out_model);
		if(empty($model)){
			return false;
		}
		
		//取款次数 多的, 附加手续费限制
		$count = $this->where("uid",$uid)->whereBetween("created_at",[date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count();
		if($model->free_time == 0 || ($count > 0 && $count > $model->free_time)){
			$this->de_overtime_charge = $model->service_charge;  //超出收款次数后,收款手续费
		}
		if($count <=0){
			$this->is_first = 1;  //判断是否首次出款
		}else{
			$this->is_first = 0;  //不是首次出款
		}
		
		$Audits = new Audit();
		//查询出状态为0的入款id
		$list_audit = $Audits->where([["status",0],['uid',$uid],['out_id',0],['in_id','>',0]])->select('id','audit_total','in_id','created_at','updated_at')->get();
		
		//入款列表audit_ids
		$audit_moeny = 0;
		foreach ($list_audit as $audit){
			$aids[] = $audit->id;
			$ids[] = $audit->in_id;
			$audit_moeny += $audit->audit_total;
		}
		
		//处理outmoney里面的表的打码关联ID
		$this->audit_ids = implode(',', $aids);
		
		//取出相关的入款记录
		$Inmoneys = new Inmoney();
		$list_in = $Inmoneys->whereIn('id',$ids)->get();
		
		//根据每一条记录,查询他们是否满足普通优惠和存款优惠;
		$audit_all = $audit_money_2 = $audit_moeny;
		//分别是 de_discount 普通稽核,不够收回优惠金额; de_audit 常态稽核,不够扣除行政费
		$this->de_discount = $this->de_audit = 0;
		$in_audit = 0;
		foreach ($list_in as $key=>$in){
			if($in->audit <= $audit_moeny){
				$audit_moeny = $audit_moeny - $in->audit;
				$list_in[$key]->server_removed = 0;
			}else{
				$this->de_audit += $in->server_fee;  //收取行政服务费
				$list_in[$key]->server_removed = 1;
			}
			$in_audit += $in->audit;  //当前用户需要的打码量
			if($in->synthetical_audit <= $audit_money_2){
				$audit_money_2 = $audit_money_2 - $in->synthetical_audit;
				$list_in[$key]->discount_removed = 0;
			}else{
				$this->de_discoun += $in->remit_money;   //扣除之前给他的普通优惠
				$this->discount_removed = 1;     //是否扣除优惠
				$list_in[$key]->discount_removed = 1;
			}
			foreach ($list_audit as $audit){
				if($list_in[$key]->id == $audit->in_id){
					$list_in[$key]->audit_total = $audit->audit_total;
					$list_in[$key]->start_at = trim($audit->created_at);
					$list_in[$key]->end_at = trim($audit->updated_at);
				}
			}
			$list_in[$key] = $list_in[$key]->getIt(); //通过这里获取一个比较小的组合
		}
		
		$UserMoneys = new UserRelevantInfo();
		$usermoney = $UserMoneys->where("uid",$uid)->first();
		//用户资金存在,并且不等于空
		if(!empty($usermoney)){
			$this->money = $usermoney->total_money - $this->de_discoun - $this->de_audit;
		}else{
			$this->money = 0;
		}
		
		return [
			'list' => &$list_in,
			'audit_total' =>  $audit_all,  //当前用户打了多少码
			'audit' =>  $in_audit,  //总的需要稽核的打码量
			'audit_need' =>  $audit_all-$in_audit,  //还差多少可以达到稽核时标准
			'total_money' => $usermoney->total_money,    //用户账户资金
			'money' => $this->money > 0 ? $this->money : 0 ,   //用户本次稽核能提现的费用
			'de_audit' => $this->de_audit,      //收取行政服务费
			'de_discoun' => $this->de_discoun,  //扣除之前给的优惠
			'de_overtime_charge' => $this->de_overtime_charge,    //收取手续费
			'free_time' => $model->free_time,    //可免费提现次数
		];
	}
	
	/**
	 * @todo 取出资金操作,用于提出用户账户里面的资金
	 * @param float $money 取款的金额
	 * @param float $service_charge 需要扣除用户的服务费,额外收的,默认50,没有可以不收,后台工作人员设置
	 * @param float $out_money 取款模式的id
	 * @param int $uid 用户UID
	 * @return bool 取款成功或者失败,成功为非审核状态,失败表示没有插入订单
	 * **/
	public function withdraw($money = 0,$out_model = 0,$uid = 0,$manage_id = 0,$pay_scheme='MC',$service_charge=50){
		if($money <=0 || $out_model <=0 || $uid <= 0){
			$this->messages[] = '参数错误,没有办法取现!';
			return false;
		}
		
		$model = WithdrawMoneyConfig::find($out_model);
		if(empty($model)){
			return false;
		}
		
		//取款额度限制
		if($money < $model->min_money || $money >$model->max_money){
			$this->messages[] = '取款额度超出限制!';
			return false;
		}
		$this->withdraw_money = $money;		//把取款的信息放到对象
		
		$audit_info = $this->audit($out_model, $uid);
		
		if($money > $audit_info['money']-$service_charge){
			$this->messages[] = '取款额度不足!';
			return false;
		}
		$this->de_service_charge = $service_charge;		//扣除用户的服务费,额外收的,默认50,没有可以不收,后台工作人员设置
		
		//启动事务,保证修改一致性
		DB::beginTransaction();
		
		//扣除用户资金表里面的资金
		$UserMoneys = new UserRelevantInfo();
		$usermoney = $UserMoneys->where("uid",$uid)->lockForUpdate()->first();  //锁定用户的资金
		
		//实际出款金额.需要减去用户的资金
		$this->withdraw_money_actual = $money + $audit_info['de_audit'] + $audit_info['de_discoun'] + $audit_info['de_overtime_charge'] + $service_charge;
		
		$usermoney->total_money = $usermoney->total_money - $this->withdraw_money_actual;//用户资金需要减去这些
		$usermoney->withdraw_cash = $usermoney->withdraw_cash + $money;
		if($usermoney->total_money > 0 ){
			$back = $usermoney->save();
		}else{
			$this->messages[] = '资金不足以扣除其它费用,无法提现!';
			return false;
		}
		
		if($back){
			$this->uid = $uid;
			$this->withdraw_config_id = $out_model;
			$this->manage_id = $manage_id;
			$this->pay_scheme = $pay_scheme;
			$this->withdraw_type = $withdraw_type;
			$back = $this->save();
			if($back){
				$Logs = new MoneyLog();
				$Logs->uid = $uid;
				$Logs->manage_id = $manage_id;
				$Logs->in_out = 1;
				$Logs->msg = '';
				$Logs->type_id = $Logs->TYPE_WITHDRAWAL;
				$Logs->money = $manage_id>0?$this->TYPE_WITHDRAWAL_MONEY:$this->withdraw_money_actual;
				$Logs->balance = $usermoney->total_money;
				$Logs->save();
				
				DB::commit();
				return true;
			}else{
				$this->messages[] = '写入取款表失败!';
			}
		}else{
			$this->messages[] = '操作用户资金表失败!';
		}
		DB::rollBack();
		return false;
	}
	
	//前台取出,取出的时候要直接确认订单
	public function beforeWithdraw($money = 0,$out_model = 0,$uid = 0,$bank_id = 0) {
		//如果前端提现,需要先设置提现银行卡
		
		$Banks = new BankCard();
		if(empty($bank_id)){
			$bank = $Banks->getDefault($uid);
		}else{
			$bank = $Banks->getCardById($bank_id);
			if($bank->uid!=$uid){
				return false;
			}
		}
		$this->bank_name = $bank->bank_name;
		
		$back = $this->withdraw($money,$out_model,$uid,0,'MC');
		if($back){
			//这里有确认订单操作
			$back = $this->confirm();
			
		}
	}
	
}
