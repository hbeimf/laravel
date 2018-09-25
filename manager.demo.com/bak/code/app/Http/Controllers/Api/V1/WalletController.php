<?php
/**
 * 账户中心-安全中心
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/9
 * Time: 11:34
 */

namespace App\Http\Controllers\Api\V1;

use App\Criteria\WalletCriteria;
use App\Repository\WalletRepository;
use App\Transformer\WalletTransformer;
use Validator;

use App\Http\Controllers\Api\V1\Controller as BaseController;

class WalletController extends BaseController
{
    protected $validator;

    protected $repo;

    public function __construct(WalletRepository $_repo)
    {
        $this->repo = $_repo;
//        $this->validator = $_validator;

    }

    /**
     * 是否绑定了银行卡
     *
     * @SWG\Get(
     *      path="/v1/client/is_bind",
     *      tags={"h5-安全中心"},
     *      summary="是否绑定了银行卡",
     *      produces={"application/json"},
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response=200,
     *          description="data有值代表有绑定",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  )
     *              ),
     *
     *          )
     *		)
     * )
     */
    public function isBind() {
        $this->repo->pushCriteria(WalletCriteria::class);
        $posts = $this->repo->scopeQuery(function($query) {
            return $query->limit(1);
        })->get(['id']);
        return $this->response()->collection($posts, new WalletTransformer());

    }

}