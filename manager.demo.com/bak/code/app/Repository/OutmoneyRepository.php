<?php

namespace App\Repository;


use App\Repository\Criteria\RangeRequestCriteria;
use App\Validator\OutmoneyValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;

class OutmoneyRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'user.name' => 'like',
        'status' => '=',
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
        $this->pushCriteria(app(RangeRequestCriteria::class));
    }

    public function validator()
    {
        return OutmoneyValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\OutMoney::class;
    }



    public function getRecord($id)
    {

//        return $this->with([
//            'users' => function($query){
//                return $query->select(['name as username']);
//             }
//        ])->find($id);

        return DB::table('bw_outmoney as m')
            ->select('m.withdraw_money_actual','m.status as withdraw_status ','m.remark','i.level_id', 'u.name as username', 'u.created_at as register_datetime',
                'i.register_ip','i.parent_uid','r.total_money','b.bank_name','b.card_num','b.user_name as bank_username','b.bank_branch')
            ->leftJoin('users as u', 'm.uid','=','u.id')
            ->leftJoin('bw_user_info as i', 'm.uid','=','i.uid')
            ->leftJoin('bw_user_relevant_info as r','m.uid','=','r.uid')
            ->leftJoin('bw_bank_card as b','m.bank_card_id','=','b.id')
            ->where('m.id','=',$id)->first();




    }

    /**
     * @param $out_id bw_outmoney.id
     * @param $uid bw_user_relevant_info.id 用户账号ID
     * @throws \Throwable
     */
    public function confirm($out_id, $uid)
    {
        DB::beginTransaction();
        try{
            $audit_ids = '';

            // 更新bw_audit，标记为结束打码
            DB::table('bw_audit')->
                where(['status'=>0, 'out_id'=>0])->
                where('uid', 'in',$audit_ids)->
                orderBy('start_at','desc')->
                update(['end_at'=>time(),'status'=>1]);

            // 插入一条新的出款记录
            DB::table('bw_audit')->insert([
                'uid' => $uid,
                'start_at' => time(),
                'out_id' => $out_id

            ]);

            // 从用户账号的总金额扣除实际出款
            $withdraw_actual = DB::table('bw_outmoney')->where('id','=',1)->get()->first()->withdraw_money_actual;
            $account  = DB::table('bw_user_relevant_info')->where('id','=',$uid)->get()->first();
            $total  = $account->total_money - $withdraw_actual;
            $withdraw_total = $account->withdraw_cash +  $withdraw_actual;
            DB::table('bw_user_relevant_info')
                ->where('id','=',$uid)
                ->update([
                    'total_money'=>$total,
                    'withdraw_cash' => $withdraw_total
                ]);


            //更新outmoney 状态
            DB::table('bw_outmoney')->
                where(['id'=>$out_id])->
                update(['status'=>1]);

            DB::commit();


        }catch (\Exception $e){

            DB::rollBack();
        }

    }

    public function refuse()
    {

    }
}