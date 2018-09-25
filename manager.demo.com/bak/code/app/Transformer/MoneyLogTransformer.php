<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\MoneyLog as Model;


class MoneyLogTransformer extends TransformerAbstract
{

    /**
     * Transform the Outmoney entity
     * @param App\Http\Model\OutMoney $model
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }
}


