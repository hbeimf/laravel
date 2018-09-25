<?php

namespace App\Repository;


use App\Validator\AgentReturnValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class AgentReturnRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'nickname' => 'like'
    ];



    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return AgentReturnValidator::class;
    }

    function model()
    {
        return \App\Http\Model\AgentReturn::class;
    }
}