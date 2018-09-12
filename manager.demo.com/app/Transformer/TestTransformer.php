<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\Test;


class TestTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(Test $model)
    {
        return $model->toArray();
    }
}


