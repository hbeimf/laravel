<?php

namespace App\Repository;

use App\Validator\WithdrawMoneyConfigValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class WithdrawMoneyConfigRepository extends BaseRepository {

	protected $fieldSearchable = [
		'name' => 'like',
	];

	public function boot() {
		parent::boot();
		$this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
	}

	public function validator() {
		return WithdrawMoneyConfigValidator::class;
	}

	function model() {
		// TODO: Implement model() method.
		return \App\Http\Model\WithdrawMoneyConfig::class;
	}
}