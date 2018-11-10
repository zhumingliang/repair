<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 4:07 PM
 */

namespace app\api\model;


use think\Model;

class PaymentsV extends Model
{

    protected $hidden = ['shop_id', 'u_id'];

    public static function getListForShop($shop_id, $u_id, $page, $size)
    {
        $sql = '( shop_id =0  AND   u_id = ' . $u_id . ') ';

        $list = self::where('shop_id', $shop_id)
            ->whereOrRaw($sql)
            ->field('shop_id,u_id,order_name,order_time,type,shop_money as money')
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page]);
        $list = $list->toArray();
        $data = $list['data'];
        if (count($data)) {
            $data = self::preShopList($data);
        }
        $list['data'] = $data;

        return $list;

    }

    private static function preShopList($list)
    {
        foreach ($list as $k => $v) {
            if ($v['type'] == 3 || $v['type'] == 4 || $v['type'] == 5) {
                $v['money'] = 0 - $v['money'];
            }
            unset($list[$k]['type']);
        }

        return $list;


    }

    public static function getListForNormal($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->order('order_time desc')
            ->paginate($size, false, ['page' => $page]);
        $list = $list->toArray();
        $data = $list['data'];
        if (count($data)) {
            $data = self::preNormalList($data);
        }
        $list['data'] = $data;

        return $list;

    }

    private static function preNormalList($list)
    {
        foreach ($list as $k => $v) {

            $list[$k]['money'] = 0 - $v['money'];

            unset($list[$k]['type']);
        }

        return $list;


    }
}