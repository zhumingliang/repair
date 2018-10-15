<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 10:49 PM
 */

namespace app\api\service;


use app\api\model\ShopT;
use app\lib\exception\OrderException;

class OrderService
{
    public static function taking($d_id, $u_id)
    {
        $shop_id = ShopT::getShopId($u_id);
        if (empty($shop_id)) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '该用户店铺状态不正常',
                    'errorCode' => 150002
                ]
            );

        }
        //保存信息
        $res=De


    }

}