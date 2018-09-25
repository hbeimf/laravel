<?php
/**
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/9/13
 * Time: 16:35
 */
namespace App\Criteria;

use App\Http\Model\BankCard;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;

class WalletCriteria implements CriteriaInterface {

    public function apply($model, RepositoryInterface $repository)
    {
//        $uid = Auth::id();
        $uid = 92;
        $model = $model->where('status', BankCard::STATUS_YES)->where('uid', $uid);
        return $model;
    }
}