<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 10:23 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class GoodsV extends Model
{
    public static function getListForCMS($page, $size, $key)
    {
        $list = self::where('state', '<', CommonEnum::DELETE)
            ->where(function ($query) use ($key) {
                if (strlen($key)) {
                    $query->where('name|category', 'like', '%' . $key . '%');
                }
            })
            ->hidden(['create_time'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;

    }

    public static function getGoodsSell($id)
    {
        $info = self::where('id', $id)
            ->field('sell_num')
            ->find();
        return $info ? $info->sell_num : 0;

    }

}