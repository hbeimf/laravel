<?php

namespace App\Repository;


use App\Validator\AuditValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;

class AuditRepository extends BaseRepository
{
	
    protected $fieldSearchable = [
        'users.name' => '=',
    ];
	

	public function boot()
    {
        parent::boot();
//        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return AuditValidator::class;
    }

    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\Audit::class;
    }
	
}