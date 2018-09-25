<?php

namespace App\Repository;

use App\Validator\PermissionsValidator;
use Prettus\Repository\Eloquent\BaseRepository;

class PermissionsRepository extends BaseRepository {

	protected $fieldSearchable = [
		'name' => 'like',
	];

	public function boot() {
		parent::boot();
		$this->pushCriteria(app(\Prettus\Repository\Criteria\RequestCriteria::class));
	}

	public function validator() {
		return PermissionsValidator::class;
	}

	function model() {
		// TODO: Implement model() method.
		return \App\Http\Model\Permissions::class;
	}
}