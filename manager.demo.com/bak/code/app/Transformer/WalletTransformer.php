<?php

namespace App\Transformer;

use League\Fractal\TransformerAbstract;

use App\Http\Model\BankCard;


class WalletTransformer extends TransformerAbstract
{

    /**
     * Transform the Test entity
     * @param App\Http\Model\BankCard $model
     *
     * @return array
     */
    public function transform(BankCard $model)
    {
        return $model->toArray();
    }
}


