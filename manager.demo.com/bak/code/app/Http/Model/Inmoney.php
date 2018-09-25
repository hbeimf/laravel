<?php

namespace App\Http\Model;

/**
 * 
 * 每次创建数据时,需要先 getDeposit
 * 前端充值是 getDeposit -> recharge 获得未确认状态的充值订单
 * 后端充值是 getDeposit -> recharge 获得未确认状态的充值订单
 * 
 * **/

use Illuminate\Support\Facades\DB;

class Inmoney extends Base {

    protected $table = 'bw_inmoney';

    protected $fillable = [
        'uid', 'pay_scheme', 'in_model', 'pay_id','money','nick'
    ];
	
	public $messages = [];		//本次存款来源
//	public $pay_scheme = '';		//本次存款来源
	
//	public $money = 0;		//本次存款的额度
//	public $remit_set = 0;
//	public $remit_money = 0;   //普通优惠额度
//	public $deposit_set = 0;
//	public $deposit_money = 0;   //额外优惠额度
//	public $audit = 0;			//打码量
//	public $audit_set = 0;  //手动设置了打码量
//	public $discount_money = 0;		//额外可以放宽打码量
//	public $server_fee = 0;  //服务费
//	public $synthetical_audit = 0;		//综合稽核打码量,要求打码量达到这个值,否则收回普通优惠
//	public $status  =0;
	
	//获取用户存款信息
    public function Users() {
		return $this->hasOne('App\Http\Model\Users', 'id', 'uid')->select('id', 'name');
	}
	
	//获取支付方式信息,从这里可以获取分组信息
	public function Paytype() {
		return $this->hasOne('App\Http\Model\Paytype', 'id', 'pay_type');
	}
	
	//获取操作员信息
	public function Manages() {
		return $this->hasOne('App\Http\Model\Users', 'id', 'manage_id');
	}
	
	//获取操作员信息
	public function Deposit() {
		return $this->hasOne('App\Http\Model\DepositConfig', 'id', 'in_model');
	}
	
	//小心操作$page['data'],因为$page['data']是传址的;
	public function search($where=[],$limit=20){
		return $this->leftJoin('users', 'users.id', '=', 'uid')->where($where)->select('users.id','users.name',$this->table.'.*')->orderByRaw(DB::raw("FIELD(status,0,1,-1)"));
		$page = $this->getPages($limit);
		
		return $page;
	}
	
	public function get_list($status,$name,$money_start,$money_end,$user_group,$time_type,$time_start,$time_end,$limit=20)
	{
	    $where = [];
	    $whereRaw = '';
	    $select = "b.name as user_name, c.group_id as group_id, e.`name` as group_name, g.`name` as manage_name, j.bank, j.number, bw_inmoney.*, j.user_name as accout_name";
	    if(!is_null($status)) {
	        $where[] = ['bw_inmoney.status', '=', $status];
	    }
	    if($name) {
	        $where[] = ['b.name', 'like', "%{$name}%"];
	    }
	    if($user_group){
	        $where[] = ['c.group_id', '=', $user_group];
	    }
	    if($time_start && $time_end) {
	        $time_end = $time_end.' 23:59:59';
	        if($time_type==0) {
	            $whereRaw = "(bw_inmoney.created_at between '".$time_start."' and '".$time_end."' or bw_inmoney.updated_at between '".$time_start."' and '".$time_end."')";
	        } else if($time_type==1) {
	            $whereRaw = "(bw_inmoney.created_at between '".$time_start."' and '".$time_end."')";
	        } else if($time_type==2) {
	            $whereRaw = "(bw_inmoney.updated_at between '".$time_start."' and '".$time_end."')";
	        }
	    }
	    $page = $this->selectRaw($select)
	    ->leftJoin('users as b', 'b.id', '=', 'bw_inmoney.uid')
	    ->leftJoin('bw_user_group_bind as c', 'c.uid', '=', 'bw_inmoney.uid')
	    ->leftJoin('bw_user_group as e', 'e.id', '=', 'c.group_id')
	    ->leftJoin('users as g', 'g.id', '=', 'bw_inmoney.manage_id')
	    ->leftJoin('bw_admission as j', 'j.id', '=', 'bw_inmoney.pay_id');
	    if(count($where)>0) {
	        $page->where($where);
	    }
	    if($money_start && $money_end){
	        $page->whereBetween('bw_inmoney.money',[$money_start,$money_end]);
	    }
	    if($whereRaw!=''){
	        $page->whereRaw($whereRaw);
	    }
	    return $page->paginate($limit);
	}
	
	public function cancel_order($args)
	{
	    $this->where('id', '=', $args['id'])->update(['status'=>-1,'manage_id'=>$args['opt_id']]);
	    return $this->get_item($args['id']);
	}
	
	public function ok_order($args)
	{
	    if($this->status!=0){
	        return $this->get_item($this->id);
	    }
	    $userInfo = new UserRelevantInfo();
   	    $relevantInfo = $userInfo->where('uid', $this->uid)->first();
	    $rows = $userInfo->where('uid', $this->uid)->update([
	        'total_money'=>DB::raw("total_money+".($this->money+$this->remit_money+$this->deposit_money)),
	        'recharge_all'=>DB::raw("recharge_all+".$this->money)
	    ]);
	    if($relevantInfo){
	        $money_log = new MoneyLog();
	        $money_log->insert([
	            ['msg'=>'线下入款','uid'=>$this->uid,'manage_id'=>$args['opt_id'],'in_out'=>2,
	                'type_id'=>MoneyLog::TYPE_RECHARGE,
	                'money'=>$this->money,
	                'balance'=>($relevantInfo->total_money+$this->money)],
	            ['msg'=>'线下入款 | 优惠','uid'=>$this->uid,'manage_id'=>$args['opt_id'],'in_out'=>2,
	                'type_id'=>MoneyLog::TYPE_RECHARGE_DISCOUNTS,
	                'money'=>$this->remit_money,
	                'balance'=>($relevantInfo->total_money+$this->money+$this->remit_money)],
	            ['msg'=>'线下入款 | 额外优惠','uid'=>$this->uid,'manage_id'=>$args['opt_id'],'in_out'=>2,
	                'type_id'=>MoneyLog::TYPE_RECHARGE_DISCOUNTS_EXTRA,
	                'money'=>$this->deposit_money,
	                'balance'=>($relevantInfo->total_money+$this->money+$this->remit_money+$this->deposit_money)]
	        ]);
	    }
	    if($rows>0){
	        $audit = new Audit();
	        $audit->uid = $this->uid;
	        $audit->in_id = $this->id;
	        $rows = $audit->save();
	    }
	    if($rows>0){
	        $this->where('id', '=', $this->id)->update(['status'=>1,'manage_id'=>$args['opt_id']]);
	    }
        return $this->get_item($this->id);
	}
	
	public function get_item($id)
	{
	    $select = "b.name as user_name, c.group_id as group_id, e.`name` as group_name, g.`name` as manage_name, j.bank, j.number, bw_inmoney.*, j.user_name as accout_name";
	    return $this->selectRaw($select)
	    ->leftJoin('users as b', 'b.id', '=', 'bw_inmoney.uid')
	    ->leftJoin('bw_user_group_bind as c', 'c.uid', '=', 'bw_inmoney.uid')
	    ->leftJoin('bw_user_group as e', 'e.id', '=', 'c.group_id')
	    ->leftJoin('users as g', 'g.id', '=', 'bw_inmoney.manage_id')
	    ->leftJoin('bw_admission as j', 'j.id', '=', 'bw_inmoney.pay_id')
	    ->where('bw_inmoney.id','=',$id)
	    ->first();
	}
	
	/**
	 * @todo 获取特定模式下,用户充值一笔款项,能得到的优惠和放弃优惠会得出的结果.
	 * @param int $mode 存款模式的ID
	 * @param int $money 本次需要计算优惠的额度
	 * @param bool $abandon 是否放弃本次优惠,如果放弃,就不享有优惠
	 * @return array 优惠信息的数组
	 * **/
	public function getDeposit($mode = 0,$money = 0,$uid = 0,$abandon = false){
		if($mode <=0 || $money <= 0 || $uid <=0){
			$this->messages[] = '需要提供用户的存款模式,用户uid,单笔存入金额';
			return false;
		}
		
		$deposit = DepositConfig::find($mode);
		
		if(empty($deposit)){
			$this->messages[] = '没有找到对应的存款模式';
			return false;
		}
		$this->in_model = $mode;
		$this->uid = $uid;
		
		if($abandon == true && $deposit->discount_giveup ==0){
			$this->messages[] = '用户不可以放弃优惠';
			return false;
		}
		
		if($money > $deposit->max_money || $money < $deposit->min_money){
			$this->messages[] = '不满足最大或者最小充值额';
			return false;
		}
		$this->money = $money;
		
		//查询出用户充值过的次数,这里不关心用户是否放弃优惠,因为判断不复杂,所以不加入用户是否放弃优惠的判断
		$have_discount = false;
//		if(($deposit->discount_money > 0 && $money >= $deposit->discount_money) || $deposit->discount_money == 0){
		//单次充值大于最少优惠充值额度,并且设置了有优惠可以享受
		if($money >= $deposit->discount_money && $deposit->discount_type > 0){
			$count = $this->where([['uid',$uid],['status',1]])->count();
			if($deposit->discount_type == 1 && $count < 1){
				$have_discount = true;  //首次充值,并且可以获得首次优惠
			}elseif($deposit->discount_type == 3 && $count <= $deposit->discount_time){
				$have_discount = true;  //满足前n次
			}elseif($deposit->discount_type ==2 ){
				$have_discount = true;  //每次都可以获得优惠
			}
		}
		
		//用户可以进行优惠并且没有放弃优惠
		if($have_discount && !$abandon){
			$this->remit_money = $deposit->discount_proportion * $this->money /100;
			if($this->remit_money > $deposit->discount_max_money){
				$this->remit_money = $deposit->discount_max_money;
			}
		}
		
		$have_ex_discount = false;
		if($money >= $deposit->ex_discount_money && $deposit->ex_discount_type > 0){
			$count = $this->where([['uid',$uid],['status',1]])->count();
			if($deposit->ex_discount_type == 1 && $count < 1){
				$have_ex_discount = true;  //首次充值,并且可以获得首次优惠
			}elseif($deposit->ex_discount_type == 3 && $count <= $deposit->ex_discount_time){
				$have_ex_discount = true;  //满足前n次
			}elseif($deposit->ex_discount_type ==2 ){
				$have_ex_discount = true;  //每次都可以获得优惠
			}
		}
		
		//这里用户不可以放弃优惠
		if($this->have_discount){
			$this->deposit_money = $deposit->ex_discount_proportion * $this->money /100;
			if($this->deposit_money > $deposit->discount_max_money){
				$this->deposit_money = $deposit->ex_discount_max_money;
			}
		}
		
		//如果常态稽核打码量启用了,计算用户本次存款可能出现的手续费.
		if($deposit->is_enable == 1){
			$this->server_fee = $this->money * $deposit->administrative_rate;				//服务费
			$this->audit = $this->money * $deposit->proportion;		//需要达到的打码量
			if($this->audit > $deposit->relaxable){
				$this->audit = $this->audit - $deposit->relaxable;	//如果设置了优惠额,并且打码量大于优惠额,减去优惠额
				$this->discount_money = $deposit->relaxable;       //把得到的优惠额存起来,这里只做记录,体现时不做实际计算
			}
		}
		
		//普通稽核优惠设定,如果没有达标,收回普通优惠中的优惠额
		if($deposit->ex_is_enable == 1){
			$this->synthetical_audit = $this->money * $deposit->code_checking;  //综合打码量需要达到的指标
		}
		
		return [
			'money' => $this->money,
			'remit_money' => $this->remit_money,
			'deposit_money' => $this->deposit_money,
			'audit' => $this->audit,
			'server_fee' => $this->server_fee,
			'synthetical_audit' => $this->synthetical_audit
		];
	}
	
	/**
	 * @todo 手动设置优惠,主要记录表示手动设置过了这个优惠,主要是后台人工入款时会有手动设置选项,前端没有这个选项,
	 * @param int $remit_money 设置的优惠额度,小于0返回false
	 * @return bool 只返回true,false
	 * **/
	public function setRemit($remit_money) {
		if(intval($remit_money) <0) return false;
		$this->remit_money = intval($remit_money);
		$this->remit_set = 1;
		return true;
	}
	
	/**
	 * @todo 手动设置优惠,主要记录表示手动设置过了这个优惠,主要是后台人工入款时会有手动设置选项,前端没有这个选项
	 * @param int $$remit_money 设置的优惠额度
	 * @return bool 只返回true
	 * **/
	public function setDeposit($deposit_money) {
		if(intval($deposit_money) <0) return false;
		$this->deposit_money = intval($deposit_money);
		$this->deposit_set = 1;
		return true;
	}
	
	
	/**
	 * @todo 后台人工入款用
	 * @param int $manage_id 后台管理员ID
	 * @param string $pay_scheme MX:线下入款,MS:线上入款,MR:人工存入,主要生成序列号用的
	 * **/
	public function add($manage_id = 0,$pay_scheme = 'MR') {
		if($manage_id <=0) {
			return false;  //没有提交管理员的id,没有办法入账
		}
		
		$this->manage_id = $manage_id;
		$this->pay_scheme = $pay_scheme;
		
		return $this->tosave();  //存起来
	}
	
	/**
	 * @todo 前台用,充值用
	 * @param int $pay_type 充值方式
	 * @param string $nick 充值的微信昵称,前台充值才有,必须填写
	 * @param string $pay_scheme 充值方式 MX:线下入款,MS:线上入款,MR:人工存入,主要生成序列号用的
	 * **/
	public function recharge($pay_type = 0,$nick = '',$pay_scheme = 'MX') {
		if($pay_type <=0 || empty($nick)){
			return false;
		}
		
		$this->pay_type	= $pay_type;
		$this->nick		= $nick;
		$this->pay_scheme = $pay_scheme;
		
		return $this->tosave();  //存起来
	}
	
	protected function tosave(){
		if($this->money <=0 || $this->status !=0){
			return false;
		}
		return $this->save();
	}
	
	/**
	 * @todo 确认一笔订单,后台审核一笔订单,审核通过,把钱加入到用户资金表,插入充值流水
	 * **/
	public function confirm(){
		if($this->id <=0 || $this->status !=0){
			$this->messages[] = '订单状态错误,没有办法确认';
			return false;
		}
		$back = false;
		
		//启动事务,保证修改一致性
		DB::beginTransaction();
		$this->lockForUpdate()->find($this->id);
		$UserMoney = new UserRelevantInfo();
		$user = $UserMoney->lockForUpdate()->where('uid', $this->uid)->first();  //悲观锁,锁定其他用户的查询.防止用户的资金被别人读取
		//修改用户的资金
		$user->recharge_all = $user->recharge_all + $this->money;  //只记录实际充值的多少现金
		$user->money = $user->money + $this->money + $this->remit_money + $this->deposit_money;  //只记录实际充值的多少现金
		
		$back = $user->save(); //保存用户的资金
		
		if($back){
			$this->status = 1;	//修改该条订单状态
			$back = $this->save();		//保存修改
		}
		
		if($back){
			DB::commit();
			return true;
		}
		
		DB::rollBack();
		return false;
	}
}
