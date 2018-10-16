<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 10:49 PM
 */

namespace app\api\service;


use app\api\model\DemandOrderT;
use app\api\model\DemandOrderV;
use app\api\model\DemandT;
use app\api\model\ServiceBookingV;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use app\lib\enum\OrderEnum;
use app\lib\exception\OrderException;

class OrderService
{
    /**
     * 商家接单
     * @param $d_id
     * @param $u_id
     * @return mixed
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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
        if (!self::checkShopBond($shop_id)) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '店铺保证金不足，请支付保证金',
                    'errorCode' => 150003
                ]
            );
        }
        if (!self::checkShopForzen($shop_id)) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '店铺保已经被冻结，无法接单。',
                    'errorCode' => 150004
                ]
            );
        }

        $demand = DemandT::where('id', $d_id)->find();
        if ($demand->state == CommonEnum::STATE_IS_FAIL) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '订单已经被删除无法接单。',
                    'errorCode' => 150005
                ]
            );
        }

        if (strtotime($demand->time_begin) < time()) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '订单已失效',
                    'errorCode' => 150006
                ]
            );

        }
        //保存信息
        $db = DemandOrderT::create([
            'd_id' => $d_id,
            's_id' => $shop_id,
            'oeder_number' => makeOrderNo(),
            'pay_money' => $demand->money,
            'origin_money' => $demand->money,
            'pay_id' => CommonEnum::ORDER_STATE_INIT,
            'confirm_id' => CommonEnum::ORDER_STATE_INIT,
            'refund_id' => CommonEnum::ORDER_STATE_INIT,
            'comment_id' => CommonEnum::ORDER_STATE_INIT,
            'r_id' => CommonEnum::ORDER_STATE_INIT,
            'state' => CommonEnum::STATE_IS_OK,
            'phone_user' => CommonEnum::STATE_IS_FAIL,
            'phone_shop' => CommonEnum::STATE_IS_FAIL

        ]);

        if (!$db) {
            throw  new OrderException();
        }
        return $db->id;


    }

    /**
     * 获取订单信息
     * @param $o_id
     * @param $order_type
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOrderInfo($o_id, $order_type)
    {
        if ($order_type == CommonEnum::ORDER_IS_DEMAND) {
            return self::getDemandInfo($o_id);

        } else {
            return self::getServiceInfo($o_id);
        }

    }

    /**
     * 获取需求订单详情
     * @param $o_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getDemandInfo($o_id)
    {
        $info = DemandOrderV::where('order_id', $o_id)->hidden(['state'])->find();
        return $info;
    }

    /**
     * 获取服务订单详情
     * @param $o_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getServiceInfo($o_id)
    {
        $info = ServiceBookingV::where('order_id', $o_id)->hidden(['state'])->find();
        return $info;
    }


    public static function getDemandList($order_type, $page, $size)
    {

        $shop_id = 1;//Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {

        } else {
            return self::getDemandListForNormal($order_type, $page, $size);

        }

    }

    private static function getDemandListForNormal($order_type, $page, $size)
    {
        $u_id = 1;//Token::getCurrentUid();
        switch ($order_type) {
            case OrderEnum::DEMAND_NORMAL_TAKING:
                return DemandOrderV::takingList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_PAY:
                '';
                break;
            case OrderEnum::DEMAND_NORMAL_CONFIRM:
                '';
                break;
            case OrderEnum::DEMAND_NORMAL_COMMENT:
                '';
                break;
            case OrderEnum::DEMAND_NORMAL_COMPLETE:
                '';
                break;

        }

    }

    private function getDemandListForShop($order_type)
    {

    }


    private static function checkShopBond($shop_id)
    {
        return true;

    }

    private static function checkShopForzen($shop_id)
    {
        return true;

    }


}