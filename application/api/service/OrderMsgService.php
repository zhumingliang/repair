<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/20
 * Time: 8:35 PM
 */

namespace app\api\service;


use app\api\model\OrderMsgV;
use app\api\model\OrderNormalMsgT;
use app\api\model\OrderShopMsgT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OrderMsgException;

class OrderMsgService
{

    public static function saveNormal($u_id, $order_id, $order_type, $type)
    {

        $data = [
            'u_id' => $u_id,
            'order_id' => $order_id,
            'type' => $type,
            'order_type' => $order_type,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $msg = OrderNormalMsgT::create($data);
        if (!$msg->id) {
            throw new OrderMsgException();
        }

    }


    public static function saveShop($u_id, $order_id, $order_type, $type)
    {

        $data = [
            'u_id' => $u_id,
            'order_id' => $order_id,
            'type' => $type,
            'order_type' => $order_type,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $msg = OrderShopMsgT::create($data);
        if (!$msg->id) {
            throw new OrderMsgException();
        }
    }

    public static function getList($page, $size)
    {

        $shop_id = Token::getCurrentTokenVar('shop_id');
        $id = $shop_id ? $shop_id : Token::getCurrentUid();
        $list = OrderMsgV::where('state', CommonEnum::STATE_IS_OK)
            ->where('u_id', $id)
            ->paginate($size, false, ['page' => $page]);
        return $list;
    }

}