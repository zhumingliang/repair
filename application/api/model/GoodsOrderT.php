<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:16 AM
 */

namespace app\api\model;


use think\Model;

class GoodsOrderT extends Model
{


    public static function getListForNOComplete($u_id)
    {
        $list = self::where('express_status', '<', 3)
            ->select();
        return $list;

    }


}