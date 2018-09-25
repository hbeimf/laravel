<?php

namespace App\Transformer;

use App\Http\Model\Roles;
use League\Fractal\TransformerAbstract;

use App\Http\Model\Permissions;

class RolesTransformer extends TransformerAbstract {
    
    /**
     * Transform the Test entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(Roles $model) {
        $t = new Permissions();
        $data = $model->toArray();
        $data['permission_ids'] = $t->getPermissionByRole($data['id']);
        return $data;
    }
}
