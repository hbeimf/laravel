<?php

namespace App\Transformer;

use App\Http\Model\UserGroup;
use League\Fractal\TransformerAbstract;

use App\Http\Model\Admission;


class AdmissionTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\AgentReturn $model
     *
     * @return array
     */
    public function transform(Admission $model)
    {
        $model->group_names = UserGroup::findMany(json_decode($model->group),['name'])->pluck('name')->implode(',');
        return $model->toArray();
    }
}
