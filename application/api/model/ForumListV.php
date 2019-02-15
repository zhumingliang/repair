<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-02-15
 * Time: 22:48
 */

namespace app\api\model;


use think\Model;

class ForumListV extends Model
{
    public static function getListForSelf($u_id, $page, $size)
    {
        $forums = self::where('u_id', $u_id)
            ->where('state', '<', 4)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $forums;

    }

    public static function getListForAll($page, $size)
    {
        $forums = self::where('state', '=', 2)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $forums;

    }

}