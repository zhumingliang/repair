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
            ->field('id,code_number,cover,count,score,status,comment_id,create_time,express,express_code,express_no,goods_name')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForMINIWithNoSend($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 1)
            ->field('id,code_number,cover,count,score,status,comment_id,create_time,express,express_code,express_no,goods_name')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForMINIWithNoReceive($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 2)
            ->field('id,code_number,cover,count,score,status,comment_id,create_time,express,express_code,express_no,goods_name')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }


    public static function getListForMINIWithNoComment($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('status', 3)
            ->where('comment_id', 0)
            ->field('id,code_number,cover,count,score,status,comment_id,create_time,express,express_code,express_no,goods_name')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForCMSWithALL($page, $size, $key)
    {
        $list = self::order('create_time desc')
            ->where(function ($query) use ($key) {
                if ($key && strlen($key)) {
                    $query->where('name|goods_name|express|code_number', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);
        return $list;

    }

    public static function getListForCMSWithNoSend($page, $size, $key)
    {

        $list = self::where('status', 1)
            ->where(function ($query) use ($key) {
                if ($key && strlen($key)) {
                    $query->where('name|goods_name|express|code_number', 'like', '%' . $key . '%');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

    public static function getListForCMSWithComplete($page, $size, $key)
    {
        $list = self::where('status', 3)
            ->where(function ($query) use ($key) {
                if ($key && strlen($key)) {
                    $query->where('name|goods_name|express|code_number', 'like', '%' . $key . '%');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }


}