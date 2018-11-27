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

    /**
     * 已预约
     * 1.在规定时间内商家确认订单
     * @param $u_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function bookingList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('shop_confirm', CommonEnum::STATE_IS_FAIL)
            //->whereTime('time_begin', '>', date('Y-m-d H:i'))
           // ->whereTime('order_time', '>', $shop_confirm_limit)
            ->paginate($size, false, ['page' => $page])->toArray();
        return $list;


    }

    public static function bookingCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));

        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('shop_confirm', CommonEnum::STATE_IS_FAIL)
         //   ->whereTime('order_time', '>', $shop_confirm_limit)
            ->count();
        return $count;


    }

    /**
     * 待付款
     * 规定时间内未付款
     * @param $u_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function payList($u_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        // $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            //->whereTime('time_begin', '>', date('Y-m-d H:i'))
           // ->whereTime('order_time', '>', $pay_limit)
            ->where('shop_confirm', CommonEnum::STATE_IS_OK)
            ->where('pay_id', CommonEnum::ORDER_STATE_INIT)
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }


    public static function payListCount($u_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        // $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            //->whereTime('time_begin', '>', date('Y-m-d H:i'))
          //  ->whereTime('order_time', '>', $pay_limit)
            ->where('shop_confirm', CommonEnum::STATE_IS_OK)
            ->where('pay_id', CommonEnum::ORDER_STATE_INIT)
            ->count();

        return $count;

    }

    /**
     * 待确认
     * 未超出待确认时间设置
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


        $count = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;

    }

    /**
     * 待评价
     * @param $u_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function commentList($u_id, $page, $size)
    {

        $list = self::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('confirm_id', '=', 1)
            ->where('comment_id', '=', CommonEnum::ORDER_STATE_INIT)
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function commentListCount($u_id)
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
     * @return \think\Paginator
     * @throws \think\exception\DbException
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
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';


        $sql = '( confirm_id = 1 ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999  AND order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';

        $list = self::where('u_id', $u_id)
            ->where('normal_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }

    public static function completeListCount($u_id)
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
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999  AND order_time < ' . $user_confirm_limit . ') ';
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
     * 待确认
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
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('shop_confirm', '=', CommonEnum::STATE_IS_FAIL)
            // ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            //->whereTime('order_time', '>', $shop_confirm_limit)
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }

    public static function shopConfirmCount($s_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $shop_confirm = $orderTime['shop_confirm'];
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));


        $count = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('shop_confirm', '=', CommonEnum::STATE_IS_FAIL)
            // ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            //->whereTime('order_time', '>', $shop_confirm_limit)
            ->count();

        return $count;
    }

    /**
     * 待服务
     * 1.用户已经支付-商家没有去服务
     * 2.商家已确认-用户在规定时间内没有支付
     * @param $s_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function service($s_id, $page, $size)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $sql = ' ( pay_id <> 99999 AND service_begin = 2)';
        $sql .= ' OR ';
        $sql .= '( shop_confirm =1 AND  pay_id  = 99999  AND  order_time > ' . $pay_limit . ') ';

        $list = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }

    public static function serviceCount($s_id)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $pay = $orderTime['pay'];
        $pay_limit = date('Y-m-d H:i', strtotime('-' . $pay . ' minute',
            time()));
        $pay_limit = 'date_format("' . $pay_limit . '","%Y-%m-%d %H:%i")';

        $sql = ' ( pay_id <> 99999 AND service_begin = 2)';
        $sql .= ' OR ';
        $sql .= '( shop_confirm =1 AND  pay_id  = 99999  AND  order_time > ' . $pay_limit . ') ';

        $count = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;
    }


    /**
     * 服务中
     * 用户已经支付-商家去服务-用户在规定时间是没有确认完成/协商
     * @param $s_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function serviceIng($s_id, $page, $size)
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
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }

    public static function serviceIngCount($s_id)
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


        $count = self::where('shop_id', $s_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('service_begin', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;
    }

    /**
     * 已完成
     * 1.用户已评价订单
     * 2.用户已经支付，但是没有在规定时间内确认
     * 3.用户确认订单时，选择协商订单，并且超出规定协商时间
     * @param $s_id
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
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
        $sql .= 'OR';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';

        $list = self::where('shop_id', $s_id)
            ->where('shop_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
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
        $sql .= 'OR';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';

        $count = self::where('shop_id', $s_id)
            ->where('shop_delete', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->count();

        return $count;
    }

//-----------------------------后台报表类数据接口------------------------------------

    /**
     * 未完成订单：
     * 1.规定时间内未确认
     * 2.商户已确认-用户规定时间内未支付
     * 3.用户已经支付-规定时间内用户未确定订单
     * 4.用户在规定时间内处于协商
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


  /*      $sql = '( shop_confirm =2  AND  order_time > ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time > ' . $pay_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';*/

        $sql = '( shop_confirm =2 ) ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 )';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999 AND confirm_id = 99999 AND order_time > ' . $user_confirm_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND consult_time > ' . $consult_limit . ')';


        $list = self::whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_number|user_phone', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }

    /**
     * 待评论订单
     * 1.订单已经支付并且确认完工订单，但是没有评价
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function readyCommentForReport($key, $page, $size)
    {
        $list = self::where('confirm_id', CommonEnum::STATE_IS_OK)
            ->where('comment_id', CommonEnum::ORDER_STATE_INIT)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_number|user_phone', 'like', '%' . $key . '%');
                }
            })
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


        $sql = '( confirm_id = 1  ) ';
        $sql .= ' OR ';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( confirm_id = 2 AND  consult_time < ' . $consult_limit . ')';

        $list = self::whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_number|user_phone', 'like', '%' . $key . '%');
                }
            })
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
        $sql .= 'OR';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =2 AND order_time > ' . $shop_confirm_limit . ')';
        $sql .= 'OR';
        $sql .= ' ( pay_id = 99999 AND shop_confirm =1 AND order_time > ' . $pay_limit . ')';


        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('order_number|user_phone', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;

    }


}