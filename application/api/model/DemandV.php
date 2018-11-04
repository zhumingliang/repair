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
        $shop_confirm_limit = date('Y-m-d H:i', strtotime('-' . $shop_confirm . ' minute',
            time()));
        $shop_confirm_limit = 'date_format("' . $shop_confirm_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( shop_confirm =2  AND  order_time < ' . $shop_confirm_limit . ') ';
        $sql .= ' OR ';
        $sql .= ' ( order_id = 0 )';

        // $sql_area = preJoinSql($province, $city, $area);
        $list = self::where('d_state', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            // ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->where('type', $type)
            ->where('area', $area)
            // ->whereRaw('o_id = 0 OR (shop_confirm =2 AND date_format(order_time,"%Y-%m-%d %H:%i") > date_format("' . $time_now . '","%Y-%m-%d %H:%i")) ')
            ->field('id,des,money,latitude,longitude,area')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;
    }

}