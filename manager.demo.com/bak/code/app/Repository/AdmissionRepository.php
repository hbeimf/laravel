<?php

namespace App\Repository;

use App\Validator\AdmissionValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class  AdmissionRepository extends BaseRepository
{
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return AdmissionValidator::class;
    }

    function model()
    {
        return \App\Http\Model\Admission::class;
    }
}