<?php
/**
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/8/10
 * Time: 14:28
 */
namespace App\Helper;

class Fn{
    /**
     * 生成随机字符串
     * @param $len integer
     * @param $type integer
     * @return string
     */
    static function getRandStr($len = 4, $type = 1)
    {
        //创建随机字符
        if($type == 1) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        } elseif ($type == 2) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        } else if ($type == 3) {
            $chars = "0123456789";
        } else if($type == 4) {
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        }

        return mb_substr(str_shuffle($chars), 0, $len);
    }

    /**
     * 对象转数组
     * */
    public static function objToArray($obj) {
        if(empty($obj)) {
            return [];
        }
        return json_decode(json_encode($obj), true);
    }
}