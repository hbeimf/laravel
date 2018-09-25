<?php
/**
 * 文件上传
 *
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/16
 * Time: 11:21
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;
use App\Helper\UploadFiles;
use Illuminate\Support\Facades\URL;

class UploadController extends CommonController
{

    /**
     * 文件上传
     *
     * @SWG\Post(
     *     path="/api/upload",
     *     tags={"api-common"},
     *     summary="文件上传",
     *     description="请求该接口需要先登录。",
     *     consumes={"multipart/form-data"},
     *     security={
     *          {
     *              "api-token":{}
     *          }
     *      },
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="文件名",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="fileName",
     *                  type="string",
     *                  description="文件名"
     *             )
     *         )
     *     )
     * )
     */
    public function upload(Request $request) {
        try{
//            $uid = $this->getUid();
            $uploadFiles = new UploadFiles();
            $fileInfo = $uploadFiles->upfile($request);
            $this->result['data'] = [
                'id'  => $fileInfo['id'],
                'fileName'  => URL::asset($fileInfo['path']),
            ];

        } catch (\Exception $e) {
            $this->result = [
                'code'  => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
        }

        return response()->json($this->result);
    }

}