<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\BankCard as Model;


class BankListTransformer extends TransformerAbstract
{

    /**
     * Transform the BankCard entity
     * @param App\Http\Model\BankList $model
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }
}


