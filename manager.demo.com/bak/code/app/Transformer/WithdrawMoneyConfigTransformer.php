<?php

namespace App\Transformer;

use App\Http\Model\WithdrawMoneyConfig;
use League\Fractal\TransformerAbstract;

class WithdrawMoneyConfigTransformer extends TransformerAbstract {

	/**
	 * Transform the Test entity
	 * @param App\Http\Model\Test $model
	 *
	 * @return array
	 */
	public function transform(WithdrawMoneyConfig $model) {
		return $model->toArray();
	}
}
