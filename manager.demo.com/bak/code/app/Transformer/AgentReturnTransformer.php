<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\AgentReturn;


class AgentReturnTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\AgentReturn $model
     *
     * @return array
     */
    public function transform(AgentReturn $model)
    {

        return $model->toArray();
    }
}
