<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\Admission;


class FundTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\Admission $model
     *
     * @return array
     */
    public function transform(Admission $model)
    {
        return $model->toArray();
    }
}


