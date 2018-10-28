<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/17
 * Time: 4:03 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class ServiceOrderV extends Model
{
    //-----------------------------用户订单数据接口------------------------------------

    public static function bookingList($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            // ->where('phone_user', CommonEnum::STATE_IS_FAIL)
            // ->where('phone_shop', CommonEnum::STATE_IS_FAIL)
            ->where('shop_confirm', CommonEnum::STATE_IS_FAIL)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            //->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone,area,address')
            ->paginate($size, false, ['page' => $page]);
        return $list;


    }

    public static function payList($u_id, $page, $size)
    {
        //$sql = ' ((phone_user = 1 OR phone_shop = 1) AND pay_id=' . CommonEnum::ORDER_STATE_INIT . ')';

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->where('shop_confirm', CommonEnum::STATE_IS_OK)
            ->where('pay_id', CommonEnum::ORDER_STATE_INIT)
            // ->whereRaw($sql)
            // ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
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
            // ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    public static function commentList($u_id, $page, $size)
    {

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', 1)
            ->where('comment_id', '=', CommonEnum::ORDER_STATE_INIT)
            // ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
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
            //  ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

//-----------------------------店铺订单数据接口------------------------------------

    public static function shopConfirm($s_id, $page, $size)
    {
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            // ->where('phone_user', '=', CommonEnum::STATE_IS_FAIL)
            ->where('pay_id', '=', CommonEnum::ORDER_STATE_INIT)
            // ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    public static function service($s_id, $page, $size)
    {
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', '<>', CommonEnum::ORDER_STATE_INIT)
            ->where('service_begin', '=', CommonEnum::STATE_IS_FAIL)
            //->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    public static function serviceIng($s_id, $page, $size)
    {
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', '<>', CommonEnum::ORDER_STATE_INIT)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', CommonEnum::ORDER_STATE_INIT)
            //->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    public static function shopComplete($s_id, $page, $size)
    {
        $day = 7;
        $time_limit = date('Y-m-d', strtotime('-' . $day . ' day',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d")';


        $sql = '( confirm_id =2  AND   order_time < ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' (confirm_id = 1)';

        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            //  ->field('order_id, source_id,shop_id,source_name,time_begin,time_end,origin_money,update_money,phone_shop,phone_user,shop_phone,user_phone')
            ->paginate($size, false, ['page' => $page]);


        return $list;
    }

//-----------------------------后台报表类数据接口------------------------------------

    /**
     * 未完成订单：
     * 1.订单未取消
     * 2.用户支付了订单
     * 3.规定时间内用户未确定订单
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function noCompleteForReport($key, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));

        $sql = '( shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time < ' . $pay_limit . ')';

        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone|', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    /**
     * 待评论订单
     * 1.订单已经支付并且确认完工订单，但是没有评价
     * @param $key
     * @param $page
     * @param $size
     * @return mixed
     */
    public static function readyCommentForReport($key, $page, $size)
    {
        $list = self::where('confirm_id', CommonEnum::STATE_IS_OK)
            ->where('comment_id', CommonEnum::ORDER_STATE_INIT)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone|', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    /**
     * 已完成订单
     * 1.用户已评价订单
     * 2.用户已经支付，但是没有在规定时间内确认
     * 3.用户确认订单时，选择协商订单，并且超出规定协商时间
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @return mixed
     */
    public static function completeForReport($key, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));

        $sql = '( comment_id <> 99999 ) ';
        $sql .= 'OR';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 9999  order_time > ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  order_time > ' . $consult_limit . ')';

        $list = self::whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone|', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;
    }

    /**
     * 全部状态（有效订单）
     * 1.用户已支付
     * 2.用户未支付：商家未确定，规定时间内商家未确认
     * 3.用户未支付：商家已确定，规定时间内商家未确认
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function allForReport($key, $page, $size)
    {

        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));

        $sql = '( pay_id <> 99999 ) ';
        $sql .= 'OR';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =2 order_time < ' . $shop_confirm_limit . ')';
        $sql .= 'OR';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =1 order_time < ' . $pay_limit . ')';


        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone|', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }


}