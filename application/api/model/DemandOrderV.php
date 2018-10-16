<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/16
 * Time: 2:41 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class DemandOrderV extends Model
{
    public static function takingList($u_id, $page, $size)
    {
        $sql = '';
        $list = DemandUserV::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw('')
            ->where('phone_user', CommonEnum::STATE_IS_FAIL)
            ->where('phone_user', CommonEnum::STATE_IS_FAIL)
            ->whereTime('time_begin', date('Y-m-d'))
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;


    }

    public static function payList($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('phone_user', CommonEnum::STATE_IS_FAIL)
            ->where('phone_user', CommonEnum::STATE_IS_FAIL)
            ->whereTime('time_begin', date('Y-m-d'))
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function confirmList($u_id)
    {

    }

    public static function commentList($u_id)
    {

    }

    public static function completeList($u_id)
    {

    }


}