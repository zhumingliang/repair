<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 10:02 PM
 */

namespace app\api\model;


use think\Model;

class ShopServiceV extends Model
{
    public static function services($page, $size, $key)
    {
        $list = self::where(function ($query) use ($key) {
            if ($key) {
                $query->where('service_name', 'like', '%' . $key . '%');
            }
        })
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

}