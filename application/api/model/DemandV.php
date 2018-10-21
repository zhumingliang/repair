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
    public static function getList($type, $province, $city, $area, $page, $size)
    {

        $time_now = date('Y-m-d H:i', strtotime('-20 minute', time()));
        $sql = preJoinSql($province, $city, $area);
        $list = self::where('d_state', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->whereTime('time_begin', '>', date('Y-m-d H:i'))
            ->where('type', $type)
            ->whereRaw('o_id = 0 OR date_format(order_time,"%Y-%m-%d %H:%i") > date_format("' . $time_now . '","%Y-%m-%d %H:%i") ')
            ->field('id,des,money,latitude,longitude,area')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;
    }

}