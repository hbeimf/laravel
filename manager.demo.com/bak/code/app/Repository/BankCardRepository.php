<?php

namespace App\Repository;


use App\Validator\BankCardValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class BankCardRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'users.name' => 'like',
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return BankCardValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\BankCard::class;
    }
}