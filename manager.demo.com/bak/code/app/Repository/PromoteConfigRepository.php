<?php

namespace App\Repository;

use App\Http\Model\PromoteConfig;
use Prettus\Repository\Eloquent\BaseRepository;

class PromoteConfigRepository extends BaseRepository
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
        return \App\Http\Model\PromoteConfig::class;
    }
}