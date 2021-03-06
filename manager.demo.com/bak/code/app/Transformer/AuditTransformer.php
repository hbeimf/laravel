<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\Audit as Model;


class AuditTransformer extends TransformerAbstract
{

    /**
     * Transform the OutMoney entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }
}


