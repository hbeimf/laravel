<?php

namespace App\Repository;


use App\Validator\DepositConfigValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class DepositConfigRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'id' => '=',
    ];
	
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }


    public function validator()
    {
        return DepositConfigValidator::class;
    }

    function model()
    {
        return \App\Http\Model\DepositConfig::class;
    }
}