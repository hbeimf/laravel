<?php

namespace App\Repository;


use App\Validator\TestValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class WalletRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'user_name' => 'like',
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return TestValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\BankCard::class;
    }
}