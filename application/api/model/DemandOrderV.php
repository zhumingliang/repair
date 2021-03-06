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
    //-----------------------------用户订单数据接口------------------------------------

    /**
     * 待接单
     * 1.未接单
     * 2.商户没有在确定的时间里确认订单
     * @param $u_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function takingList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $sql = 'order_id= 0 ';
        $sql .= ' OR ';
        $sql .= '( shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time < ' . $pay_limit . ')';

        $list = DemandUserV::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $list;
    }


    public static function takingCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $sql = 'order_id= 0 ';
        $sql .= ' OR ';
        $sql .= '( shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time < ' . $pay_limit . ')';

        $count = DemandUserV::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->whereRaw($sql)
            ->count();
        return $count;
    }

    /**
     * 获取订单是否被接
     * 1.商家未确认-超时
     * 2.商家已确认-在规定时间里未支付
     * 3.未接
     * 订单数据可被接单状态
     * @param $id
     * @return float|string
     */
    public static function getOrderToCheck($id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $sql = 'order_id= 0 ';
        $sql .= ' OR ';
        $sql .= '( order_id > 0 AND shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( order_id > 0 AND shop_confirm = 1 AND pay_id = 99999 AND order_time <' . $pay_limit . ')';

        $count = DemandUserV::where('id', '=', $id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();
        return $count;
    }

    /**
     * 待支付
     * 规定时间内未付款
     * @param $u_id
     * @param $page
     * @param $size
     * @return mixed
     */
    public static function payList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm = 2 AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ')';

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            // ->whereTime('time_begin', date('Y-m-d H:i'))
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function payCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm = 2 AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ')';

        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;

    }

    /**
     * 待确认
     * 用户已支付-未超出待确认时间设置
     * 协商中-协商未超时
     * @param $u_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function confirmList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time > ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time > ' . $consult_limit . ')';


        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function confirmCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time > ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time > ' . $consult_limit . ')';


        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();
        return $list;

    }


    /**
     * 待评价
     * @param $u_id
     * @param $page
     * @param $size
     * @return mixed
     */
    public static function commentList($u_id, $page, $size)
    {

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', 1)
            ->where('comment_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function commentCount($u_id)
    {

        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', 1)
            ->where('comment_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->count();
        return $count;

    }


    /**
     * 已完成订单
     * 1.用户已评价订单
     * 2.用户已经支付，但是没有在规定时间内确认
     * 3.用户确认订单时，选择协商订单，并且超出规定协商时间
     * @param $u_id
     * @param $page
     * @param $size
     * @return mixed
     */
    public static function completeList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';


        $list = self::where('u_id', $u_id)
            ->where('normal_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function completeCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';


        $count = self::where('u_id', $u_id)
            ->where('normal_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();
        return $count;

    }

    //-----------------------------店铺订单数据接口------------------------------------

    /**
     * 待服务
     *1.已经接单-未确认-未超时
     *2.已接单-已确认-未支付-未超时
     * 3.已接单-已支付-未去服务
     * @param $s_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function service($s_id, $page, $size)
    {


        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));

        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND service_begin = 2 ) ';

        $list = self::where('shop_id', $s_id)
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }


    public static function serviceCount($s_id)
    {


        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));

        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND service_begin = 2 ) ';

        $count = self::where('shop_id', $s_id)
            ->whereRaw($sql)
            ->count();

        return $count;
    }

    /**
     * 商家待确认
     * 1.商家在规定时间内没有确认订单
     * @param $s_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function shopConfirm($s_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time > ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time > ' . $consult_limit . ')';

        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }


    public static function shopConfirmCount($s_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time > ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time > ' . $consult_limit . ')';

        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $list;
    }

    /**
     * 已完成订单
     * 1.用户已评价订单
     * 2.用户已经支付，但是没有在规定时间内确认
     * 3.用户确认订单时，选择协商订单，并且超出规定协商时间
     * @param $s_id
     * @param $page
     * @param $size
     * @return mixed
     */
    public static function shopComplete($s_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';


        $list = self::where('shop_id', $s_id)
            ->where('shop_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }


    public static function shopCompleteCount($s_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';


        $count = self::where('shop_id', $s_id)
            ->where('shop_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;
    }

    /**
     * 店铺获取未完成订单
     * 1.规定时间内未确认
     * 2.商户已确认-用户规定时间内未支付
     * 3.用户已经支付-规定时间内用户未确定订单
     * 4.用户在规定时间内处于协商
     * @param $s_id
     * @return mixed
     */
    public static function getCountForShop($s_id)
    {

        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));

        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ' ) ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';


        $count = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();
        return $count;
    }

    /**
     *用户获取未完成订单
     * 1.规定时间内未确认
     * 2.商户已确认-用户规定时间内未支付
     * 3.用户已经支付-规定时间内用户未确定订单
     * 4.用户在规定时间内处于协商
     * @param $u_id
     * @return mixed
     */
    public static function getCountForNormal($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';

        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();
        return $count;

    }


//-----------------------------后台报表类数据接口------------------------------------

    /**
     * 未完成订单：
     * 1.订单未取消
     * 2.商家规定时间内没有确认
     * 3.用户规定时间内没有支付
     * 4.用户支付了订单
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
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';

        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone', 'like', '%' . $key . '%');
                }
            })
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

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
     * @throws \think\exception\DbException
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
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';

        $list = self::whereRaw($sql)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone', 'like', '%' . $key . '%');
                }
            })
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

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
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( pay_id <> 99999 ) ';
        $sql .= ' OR ';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =2 AND  order_time > ' . $shop_confirm_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =1 AND  order_time > ' . $pay_limit . ')';

        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_num|user_phone', 'like', '%' . $key . '%');
                }
            })
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }


    public static function checkNoComplete($shop_id, $u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $pay = $orderTime['pay'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';

        $count = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where('shop_id', $shop_id)
            ->where('u_id', $u_id)
            ->count();
        return $count;
    }


}