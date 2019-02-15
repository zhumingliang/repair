<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-02-14
 * Time: 00:30
 */

namespace app\api\model;


use think\Model;

class ForumV extends Model
{
    const ALL = 2;
    const READY = 1;

    public static function getListForCms($type, $page, $size, $key)
    {
        $pagingData = self::where(function ($query) use ($type) {
            if ($type == self::READY) {
                $query->where('state', '=', self::READY);
            } else if ($type == self::ALL) {
                $query->where('state', '<', 4);
            }
        })
            ->where(function ($query) use ($key) {
                if (strlen($key)) {
                    $query->where('title|nickName', 'like', '%' . $key . '%');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;

    }

}