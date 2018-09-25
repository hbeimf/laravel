<?php
/**
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/9/13
 * Time: 16:35
 */
namespace App\Criteria;

use App\Http\Model\Admission;
use App\Http\Model\UserInfo;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;
use Illuminate\Support\Facades\DB;

class FundCriteria implements CriteriaInterface {

    public function apply($model, RepositoryInterface $repository)
    {
        $uid = Auth::id();
        $group_id = UserInfo::where('uid', $uid)->value('group_id');
        $group_id = $group_id ?? 0;
        $model = $model->where('status','=', Admission::STATUS3_YES )->whereRaw('FIND_IN_SET('.$group_id.', `group`)');
        return $model;
    }
}