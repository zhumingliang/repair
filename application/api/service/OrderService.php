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
use app\api\model\OrderCommentImgT;
use app\api\model\OrderCommentT;
use app\api\model\ServiceBookingV;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use app\lib\enum\OrderEnum;
use app\lib\exception\OrderException;
use think\Db;
use think\Exception;

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
            'service_begin' => CommonEnum::STATE_IS_FAIL,
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

    /**
     *  获取订单列表
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function getDemandList($order_type, $page, $size)
    {

        $shop_id = Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {
            return self::getDemandListForShop($shop_id, $order_type, $page, $size);
        } else {
            return self::getDemandListForNormal($order_type, $page, $size);

        }

    }


    /**
     * 获取服务订单列表
     * @param $order_type
     * @param $page
     * @param $size
     * @return mixed
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function getServiceList($order_type, $page, $size)
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {
            return self::getServiceListForShop($shop_id, $order_type, $page, $size);
        } else {
            return self::getServiceListForNormal($order_type, $page, $size);

        }

    }

    /**
     * 保存订单评论
     * @param $params
     * @throws Exception
     */
    public static function saveComment($params)
    {

        Db::startTrans();
        try {
            $imgs = $params['imgs'];
            unset($params['imgs']);
            $params['u_id'] = 1;//Token::getCurrentUid();
            $params['state'] = CommonEnum::STATE_IS_OK;
            $obj = OrderCommentT::create($params);
            if (!$obj) {
                throw new OrderException(
                    ['code' => 401,
                        'msg' => '新增评论失败',
                        'errorCode' => 150010
                    ]
                );
            }
            $relation = [
                'name' => 'o_id',
                'value' => $obj->id
            ];
            $res = self::saveImageRelation($imgs, $relation);
            if (!$res) {
                Db::rollback();
                throw new OrderException(
                    ['code' => 401,
                        'msg' => '创建评论图片关联失败',
                        'errorCode' => 150011
                    ]
                );
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    /**
     * 保存评论和图片关联
     * @param $imgs
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    private static function saveImageRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $OCI = new OrderCommentImgT();
        $res = $OCI->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }

    private static function getDemandListForNormal($order_type, $page, $size)
    {
        $u_id = Token::getCurrentUid();
        switch ($order_type) {
            case OrderEnum::DEMAND_NORMAL_TAKING:
                return DemandOrderV::takingList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_PAY:
                return DemandOrderV::payList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_CONFIRM:
                return DemandOrderV::confirmList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMMENT:
                return DemandOrderV::commentList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMPLETE:
                return DemandOrderV::completeList($u_id, $page, $size);
                break;

        }

    }

    private static function getDemandListForShop($shop_id, $order_type, $page, $size)
    {

        if ($order_type == OrderEnum::DEMAND_SHOP_TAKING) {
            return DemandOrderV::service($shop_id, $page, $size);

        } else if ($order_type == OrderEnum::DEMAND_SHOP_CONFIRM) {
            return DemandOrderV::shopConfirm($shop_id, $page, $size);

        } else if ($order_type == OrderEnum::DEMAND_SHOP_COMPLETE) {
            return DemandOrderV::shopComplete($shop_id, $page, $size);

        }


    }


    private static function getServiceListForNormal($order_type, $page, $size)
    {
        $u_id = Token::getCurrentUid();
        switch ($order_type) {
            case OrderEnum::DEMAND_NORMAL_TAKING:
                return DemandOrderV::takingList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_PAY:
                return DemandOrderV::payList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_CONFIRM:
                return DemandOrderV::confirmList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMMENT:
                return DemandOrderV::commentList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMPLETE:
                return DemandOrderV::completeList($u_id, $page, $size);
                break;

        }

    }

    private static function getServiceListForShop($shop_id, $order_type, $page, $size)
    {

        if ($order_type == OrderEnum::DEMAND_SHOP_TAKING) {
            return DemandOrderV::service($shop_id, $page, $size);

        } else if ($order_type == OrderEnum::DEMAND_SHOP_CONFIRM) {
            return DemandOrderV::shopConfirm($shop_id, $page, $size);

        } else if ($order_type == OrderEnum::DEMAND_SHOP_COMPLETE) {
            return DemandOrderV::shopComplete($shop_id, $page, $size);

        }


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