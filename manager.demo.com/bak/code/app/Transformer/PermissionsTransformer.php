<?php

namespace App\Transformer;

use App\Http\Model\Permissions;
use League\Fractal\TransformerAbstract;

class PermissionsTransformer extends TransformerAbstract {
    
    /**
     * Transform the Test entity
     * @param App\Http\Model\Test $model
     *
     * @return array
     */
    public function transform(Permissions $model) {
        return $model->toArray();
    }
}
