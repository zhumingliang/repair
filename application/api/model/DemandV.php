<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/12
 * Time: 4:50 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class DemandV extends Model
{
    /**
     * 获取需求大厅列表
     * @param $type
     * @param $province
     * @param $city
     * @param $area
     * @param $page
     * @param $size
     * @return array
     * @throws \think\exception\DbException
     */
    public static function getList($type, $province, $city, $area, $page, $size)
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


        // $sql_area = preJoinSql($province, $city, $area);
        $list = self::where('d_state', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->where('type', $type)
            ->where('area', $area)
            ->field('id,des,money,latitude,longitude,area')
            ->group('id')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;
    }

}