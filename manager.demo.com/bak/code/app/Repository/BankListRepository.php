<?php

namespace App\Repository;


use App\Validator\BankListValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class BankListRepository extends BaseRepository
{

    protected $fieldSearchable = [
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return BankListValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\BankList::class;
    }
}