<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/29
 * Time: 10:37 AM
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use app\lib\enum\OrderEnum;
use think\Model;

class OrderReportV extends Model
{
    /**
     * 加盟商获取未完成订单
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function noCompleteForJoin($key, $page, $size)
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


        $sql = '( shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time < ' . $pay_limit . ')';
        $sql .= ' OR ';
        $sql .= ' ( pay_id <> 99999)';

        $province = Token::getCurrentTokenVar('province');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');
        $sql_join = preJoinSqlForGetDShops($province, $city, $area);

        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->whereRaw($sql_join)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('source_name', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    /**
     * 加盟商获取完成订单
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function completeForJoin($key, $page, $size)
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

        $sql = '( comment_id <> 99999 ) ';
        $sql .= 'OR';
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  order_time < ' . $consult_limit . ')';

        $province = Token::getCurrentTokenVar('province');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');
        $sql_join = preJoinSqlForGetDShops($province, $city, $area);



        $list = self::whereRaw($sql)
            ->whereRaw($sql_join)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('source_name', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }

    /**
     * 获取正在服务的订单
     */
    public static function serviceIng($shop_id)
    {

        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $list = self::where('shop_id', $shop_id)
            ->where('service_begin', 1)
            ->field('shop_id,source_name as order_name,user_name as username,area,address,time_begin,time_end, order_id,order_type as type')
            ->where('confirm_id', CommonEnum::ORDER_STATE_INIT)
            ->whereTime('order_time', '>', $user_confirm_limit)
            ->select();

        return $list;

    }

    /**
     * 按城市导出数据
     * @param $province
     * @param $city
     * @param $time_begin
     * @param $time_end
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function reportForCity($province, $city, $time_begin, $time_end)
    {
        $list = self::where('province', $province)
            ->where('city', $city)
            ->whereTime('order_time', 'between', [$time_begin, $time_end])
            ->field('u_id,order_time,user_phone,update_money,source_name,order_number,read_money,area')
            ->select()
            ->toArray();
        return $list;

    }


    public static function reportWithoutCity($time_begin, $time_end)
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('order_time', 'between', [$time_begin, $time_end])
            ->field('u_id,order_time,user_phone,update_money,source_name,order_number,read_money,city')
            ->select()
            ->toArray();
        return $list;

    }


    public static function reportForJoin($province, $city, $area, $time_begin, $time_end)
    {
        $sql = preJoinSqlForGetDShops($province, $city, $area);
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('order_time', 'between', [$time_begin, $time_end])
            ->whereRaw($sql)
            ->field('u_id,order_time,user_phone,update_money,source_name,order_number,read_money,city')
            ->select()
            ->toArray();
        return $list;

    }


}