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
        $sql .= 'OR';
        $sql .= ' ( shop_confirm = 1 AND pay_id = 99999 AND order_time < ' . $pay_limit . ')';
        $sql .= 'OR';
        $sql .= ' ( pay_id <> 99999)';

        $sql_join = preJoinSqlForGetDShops(Token::getCurrentTokenVar('province'), Token::getCurrentTokenVar('city'),
            Token::getCurrentTokenVar('area'));

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
        $sql .= '( pay_id <> 99999  AND  confirm_id = 99999  order_time > ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  order_time > ' . $consult_limit . ')';

        $sql_join = preJoinSqlForGetDShops(Token::getCurrentTokenVar('province'), Token::getCurrentTokenVar('city'),
            Token::getCurrentTokenVar('area'));

        $list = self::whereRaw($sql)
            ->whereRaw($sql_join)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('source', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);

        return $list;

    }


}