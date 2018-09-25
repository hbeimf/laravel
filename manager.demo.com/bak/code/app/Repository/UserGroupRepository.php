<?php

namespace App\Repository;

use App\Validator\UserGroupValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class UserGroupRepository extends BaseRepository
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
        return UserGroupValidator::class;
    }

    function model()
    {
        return \App\Http\Model\UserGroup::class;
    }


}