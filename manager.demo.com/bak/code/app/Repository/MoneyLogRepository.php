<?php

namespace App\Repository;


use App\Validator\OutmoneyValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;

class MoneyLogRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'type_id' => '=',
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
//        return OutmoneyValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\MoneyLog::class;
    }
}