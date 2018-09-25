<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\DepositConfig;


class DepositConfigTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(DepositConfig $model)
    {
        return $model->toArray();
    }
}


