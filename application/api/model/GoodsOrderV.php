<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/17
 * Time: 10:33 AM
 */

namespace app\api\model;


use think\Model;

class GoodsOrderV extends Model
{

    public static function getListForMINIWithAll($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->field('id,code_number,cover,count,score,status,comment_id,a.create_time,express,express_code')
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForMINIWithNoSend($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 1)
            ->field('id,code_number,cover,count,score,status,comment_id,a.create_time,express,express_code')
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForMINIWithNoReceive($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 2)
            ->field('id,code_number,cover,count,score,status,comment_id,a.create_time,express,express_code')
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }


    public static function getListForMINIWithNoComment($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 3)
            ->where('comment_id', 0)
            ->field('id,code_number,cover,count,score,status,comment_id,a.create_time,express,express_code')
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForCMSWithALL($page, $size)
    {
        $list = self::order('create_desc')->paginate($size, false, ['page' => $page]);
        return $list;

    }

    public static function getListForCMSWithNoSend($page, $size)
    {

        $list = self::where('status', 1)
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForCMSWithComplete($page, $size)
    {
        $list = self::where('status', 3)
            ->order('create_desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }


}