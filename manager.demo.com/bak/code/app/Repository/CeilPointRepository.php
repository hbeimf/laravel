<?php

namespace App\Repository;

use App\Http\Model\CeilPoint;
use Prettus\Repository\Eloquent\BaseRepository;

class CeilPointRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id' => '=',
    ];
	
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }


    function model()
    {
        // TODO: Implement model() method.
        return \App\Http\Model\CeilPoint::class;
    }
}