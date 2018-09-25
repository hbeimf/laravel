<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\Inmoney;


class InmoneyTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\Inmoney $model
     *
     * @return array
     */
    public function transform(Inmoney $model)
    {
        return $model->toArray();
    }
}


