<?php

namespace App\Repository;

use App\Validator\RolesValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class RolesRepository extends BaseRepository {

	protected $fieldSearchable = [
		'name' => 'like',
	];

	public function boot() {
		parent::boot();
		$this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
	}

	public function validator() {
		return RolesValidator::class;
	}

	function model() {
		// TODO: Implement model() method.
		return \App\Http\Model\Roles::class;
	}
}
