<?php

namespace App\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Validator\LaravelValidator;

class IcomeRepository extends BaseRepository
{

    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return LaravelValidator::class;
    }

    function model()
    {
        return \App\Http\Model\Inmoney::class;
    }
    
    public function getModel()
    {
        return $this->model;
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
        $page = $this->model->selectRaw($select)
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
        return $this->scopeQuery(function($query)use($page){
            return $page;
        });
    }
}