<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\BankCard as Model;


class BankCardTransformer extends TransformerAbstract
{

    /**
     * Transform the BankCard entity
     * @param App\Http\Model\BankCard $model
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }
}


