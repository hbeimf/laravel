<?php
/**
 * 系统配置
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/9
 * Time: 11:34
 */
namespace App\Http\Controllers\Admin;

use App\Http\Model\CeilPoint;
use App\Http\Model\Domain;
use App\Http\Model\Game;
use Validator;


class ConfigController extends CommonController
{
    /**
     * 获取游戏种类配置
     *
     * @1SWG\Get(
     *      path="/api/admConfig/getGame",
     *      tags={"admin-配置相关"},
     *      summary="获取游戏种类配置",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @1SWG\Response(
     *          response="default",
     *          description="一列数据",
     *          @1SWG\Schema(
     *              type="array",
     *              @1SWG\Items(
     *
     *                  @1SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @1SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="游戏名称"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function getGame() {
        $game = Game::where('status', Game::STATUS_YES)->get(['id', 'name']);
        $this->result['data'] = $game;
        return response()->json($this->result);
    }

    /**
     * 获取游戏种类及返点上限配置
     *
     * @1SWG\Get(
     *      path="/api/admConfig/getGameMaxPoint",
     *      tags={"admin-配置相关"},
     *      summary="获取游戏种类及返点上限配置",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @1SWG\Response(
     *          response="default",
     *          description="一列数据",
     *          @1SWG\Schema(
     *              type="array",
     *              description="dd",
     *              @1SWG\Items(
     *                  @1SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @1SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="游戏名称"
     *                  ),
     *                  @1SWG\Property(
     *                      property="max_point",
     *                      type="integer",
     *                      description="上限返点"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function getGameMaxPoint() {
        $uid = CommonController::getUid();
        $ceilPoint = new CeilPoint();
        $this->result['data'] = $ceilPoint->getGameMaxPoint($uid);
        return response()->json($this->result);
    }

    /**
     * 获取推广域名配置
     *
     * @1SWG\Get(
     *      path="/api/admConfig/getDomain",
     *      tags={"admin-配置相关"},
     *      summary="获取推广域名配置",
     *      description="请求该接口需要先登录。",
     *      operationId="getMyData",
     *      security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *      @1SWG\Response(
     *          response="default",
     *          description="一列数据",
     *          @1SWG\Schema(
     *              type="array",
     *              @1SWG\Items(
     *
     *                  @1SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="id"
     *                  ),
     *                  @1SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="推广域名"
     *                  )
     *              )
     *      )
     *   )
     * )
     */
    public function getDomain() {
        $this->result['data'] = Domain::where('status', Domain::STATUS_YES)->get(['id', 'name']);
        return response()->json($this->result);
    }



}