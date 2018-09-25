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

namespace App\Helper;

use App\Http\Controllers\Api\CommonController;
use App\Http\Model\File;

class UploadFiles extends CommonController
{
    private $file;
    private $ext;
    private $type;
    private $size;
    private $originalName;
    private $realPath;  //临时文件绝对路径
    private $md5File;
    private $sha1File;
    private $uploads = 'uploads';

    public function upfile($request) {
        try{
//            $uid = $this->getUid();
            if (!$request->hasFile('file')) {
                throw new \Exception('无法获取上传文件', 1001);
            }
            $this->file = $request->file('file');
            $this->_checkFile();
            //验证文件是否上传过
            $return = $this->_exist();
            if($return) {
                return $return;
            }

            // 上传文件
            $dirPath = date('Y/m');
            // 使用我们新建的uploads本地存储空间（目录）
            $path = $this->file->store($dirPath, $this->uploads);

            //文件裁剪  composer require intervention/image <http://image.intervention.io/>

            $data = [
                'dirType'   => $this->uploads.'/'.$dirPath,
                'original_name'   => $this->originalName,
                'name'   => $this->file->hashName(),
                'size'   => $this->size,
                'md5_file'   => $this->md5File,
                'sha1_file'   => $this->sha1File,
//                'sha1_file'   => sha1_file( public_path('uploads').DIRECTORY_SEPARATOR.$path),
                'add_time'   => time(),
            ];
            $id = File::insertGetId($data);
            return [
                'path' => $data['dirType'].'/'.$data['name'],
                'id' => $id
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 文件验证
     * */
    private function _checkFile() {
        if($this->file->isValid() == false) {
            throw new \Exception('文件未通过验证', 1002);
        }
        $this->_fileInfo();
        //上传文件的类型限制
        if (starts_with($this->type, 'image/') == false) {
            throw new \Exception('文件类型错误', 1003);
        }
        //上传文件的大小限制
        if($this->size > 2*1024*1024){
            throw new \Exception('上传文件超过2MB', 1004);
        }
    }

    /**
     * 文件信息
     * */
    private function _fileInfo() {
        // 获取文件相关信息
        $this->originalName = $this->file->getClientOriginalName(); // 文件原名
        $this->ext = $this->file->getClientOriginalExtension();   // 扩展名
        $this->realPath = $this->file->getRealPath();  //临时文件的绝对路径
        $this->type = $this->file->getClientMimeType();   // image/jpeg
        $this->size = $this->file->getClientSize();
    }

    //验证文件是否已存在
    private function _exist() {
        $this->md5File = md5_file($this->realPath);
        $this->sha1File = sha1_file($this->realPath);
        $fileInfo = File::where(['md5_file' => $this->md5File, 'sha1_file' => $this->sha1File])
            ->first(['id', 'dirType', 'name']);

        if($fileInfo) {
            return [
                'path' => $fileInfo->dirType.'/'.$fileInfo->name,
                'id' => $fileInfo->id
            ];
        }
        return false;
    }
}