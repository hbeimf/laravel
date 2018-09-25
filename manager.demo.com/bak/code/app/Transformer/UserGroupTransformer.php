<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\UserGroup;


class UserGroupTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\AgentReturn $model
     *
     * @return array
     */
    public function transform(UserGroup $model)
    {

        return $model->toArray();
    }
}
