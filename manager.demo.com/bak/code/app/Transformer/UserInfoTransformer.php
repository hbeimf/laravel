<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\UserInfo;


class UserInfoTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(UserInfo $model)
    {
        return $model->toArray();
    }
}


