<?php

namespace App\Repository;


use App\Validator\UserInfoValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Http\Model\UserInfo;

class UserInfoRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'id' => '=',
		'uid' => '=',
    ];
	
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
    }

    public function validator()
    {
        return UserInfoValidator::class;
    }
	
	public function getModel() {
		return $this->model;
	}

    function model()
    {
        return \App\Http\Model\UserInfo::class;
    }
	
	public function getUserStatus() {
		$status = $this->model->getStatue();
		switch($status) {
			case 1:
				$statusName = '正常';
				break;
			case 2:
				$statusName = '异常';
				break;
			case 3:
				$statusName = '锁定';
				break;
			default:
				$statusName = '未知';
				break;
		}
		return $statusName;
	}
}