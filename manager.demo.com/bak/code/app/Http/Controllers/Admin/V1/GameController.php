<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Transformer\InmoneyTransformer;
use App\Validator\InmoneyValidator;
use App\Repository\InmoneyRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class  GameController extends BaseController
{
	
	/**
     * 推广链接列表
     *
     * @SWG\Get(
     *      path="/api/game/list",
     *      tags={"admin-推广链接-huang"},
     *      summary="推广链接列表",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @SWG\Response(
     *          response="default",
     *          description="一列数据",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="游戏名称"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function getList(Request $request) {
		
		return $this->response()->paginator($this->repo->paginate(),new TestTransformer());
    }
}