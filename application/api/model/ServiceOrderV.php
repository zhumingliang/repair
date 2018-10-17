<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/17
 * Time: 4:03 PM
 */

namespace app\api\model;


use think\Model;

class ServiceOrderV extends Model
{
    public static function takingList($u_id, $page, $size)
    {
        $minute = 20;
        $time_limit = date('Y-m-d H:i', strtotime('-' . $minute . ' minute',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d %H:%i")';
        $sql = '( phone_user = 2 AND phone_shop = 2 AND  order_time >= ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( order_id = 0)';
        $list = DemandUserV::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('time_begin', date('Y-m-d H:i'))
            ->whereRaw($sql)
            ->field('order_id,demand_name as source_name,time_begin,time_end,money as origin_money')
            ->paginate($size, false, ['page' => $page]);
        return $list;


    }

    public static function payList($u_id, $page, $size)
    {
        $minute = 20;
        $time_limit = date('Y-m-d H:i', strtotime('-' . $minute . ' minute',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( phone_user = 2 OR phone_shop = 2 AND  order_time < ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ((phone_user = 1 OR phone_shop = 1) AND pay_id=' . CommonEnum::ORDER_STATE_INIT . ')';

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('time_begin', date('Y-m-d H:i'))
            ->whereRaw($sql)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function confirmList($u_id, $page, $size)
    {

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', '<>', CommonEnum::ORDER_STATE_INIT)
            ->where('confirm_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function commentList($u_id, $page, $size)
    {

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', 1)
            ->where('comment_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function completeList($u_id, $page, $size)
    {
        $day = 7;
        $time_limit = date('Y-m-d', strtotime('-' . $day . ' day',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d")';


        $sql = '( confirm_id =2  AND comment_id = 99999 AND   order_time < ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' (comment_id <> 99999)';

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function service($s_id, $page, $size)
    {
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', '<>', CommonEnum::ORDER_STATE_INIT)
            ->where('service_id', '=', CommonEnum::STATE_IS_FAIL)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money,phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    public static function shopConfirm($s_id, $page, $size)
    {
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', '<>', CommonEnum::ORDER_STATE_INIT)
            ->where('confirm_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money,phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    public static function shopComplete($s_id, $page, $size)
    {
        $day = 7;
        $time_limit = date('Y-m-d', strtotime('-' . $day . ' day',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d")';

        $sql = '( confirm_id =2  AND comment_id = 99999 AND   order_time < ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' (comment_id <> 99999)';

        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->field('order_id,source_name,time_begin,time_end,origin_money,update_money,phone')
            ->paginate($size, false, ['page' => $page]);


        return $list;
    }

}