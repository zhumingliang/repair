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
use app\api\model\OrderReportV;
use app\api\model\OrderUserShopV;
use app\api\model\ServiceBookingT;
use app\api\model\ServiceOrderV;
use app\api\model\ShopT;
use app\api\model\SystemDemandT;
use app\api\model\SystemTimeT;
use app\lib\enum\CommonEnum;
use app\lib\enum\OrderEnum;
use app\lib\enum\RedEnum;
use app\lib\exception\OrderException;
use think\Db;
use think\Exception;

class OrderService
{
    /**
     * @param $d_id
     * @param $u_id
     * @return mixed
     * @throws Exception
     * @throws OrderException
     * @throws \app\lib\exception\OrderMsgException
     * @throws \app\lib\exception\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function taking($d_id, $u_id)
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        $shop_info = ShopT::where('id', $shop_id)->find();
        $demand = DemandT::where('id', $d_id)->find();

        if ($shop_info->frozen == 2) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '店铺保已经被冻结，无法接单。',
                    'errorCode' => 150004
                ]
            );
        }


        if ($shop_info->type != $demand->type) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '需求不在服务范围内',
                    'errorCode' => 150004
                ]
            );
        }

        if (!self::checkTaking($d_id)) {
            throw new OrderException(
                ['code' => 401,
                    'msg' => '订单已被接单',
                    'errorCode' => 150007
                ]
            );
        }

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
            'order_number' => makeOrderNo(),
            'pay_money' => 0,
            'origin_money' => $demand->money,
            'update_money' => $demand->money,
            'pay_id' => CommonEnum::ORDER_STATE_INIT,
            'confirm_id' => CommonEnum::ORDER_STATE_INIT,
            'refund_id' => CommonEnum::ORDER_STATE_INIT,
            'comment_id' => CommonEnum::ORDER_STATE_INIT,
            'r_id' => CommonEnum::ORDER_STATE_INIT,
            'state' => CommonEnum::STATE_IS_OK,
            'phone_user' => CommonEnum::STATE_IS_OK,
            'shop_confirm' => CommonEnum::STATE_IS_FAIL,
            'service_begin' => CommonEnum::STATE_IS_FAIL,
            'phone_shop' => CommonEnum::STATE_IS_OK

        ]);
        if (!$db) {
            throw  new OrderException();
        }
        //添加用户消息提示
        OrderMsgService::saveNormal($demand->u_id, $db->id, 1, 2);

        //发送消息通知商家
        (new SendMsgService($db->id, $d_id))->sendToNormal();
        return $db->id;
    }

    /**
     * 检测是否已被抢单
     * @param $id
     * @return bool
     * true 已抢
     */
    private static function checkTaking($id)
    {
        if (DemandOrderV::getOrderToCheck($id)) {
            //订单未被接
            return true;
        }
        return false;

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
        if ($info['confirm_id'] == 2) {
            $info['consult_time'] = self::getConsultTime($info['consult_time']);
        }
        return $info;
    }

    private static function getConsultTime($order_time)
    {
        $orderTime = SystemTimeT::getSystemOrderTime();
        $consult = $orderTime['consult'];
        $consult_time = date('Y-m-d H:i', strtotime('+' . $consult . ' minute',
            strtotime($order_time)));
        return $consult_time;
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
        $info = ServiceOrderV::where('order_id', $o_id)->hidden(['state'])->find();
        if ($info['confirm_id'] == 2) {
            $info['consult_time'] = self::getConsultTime($info['consult_time']);
        }
        return $info;
    }

    /**
     *  获取订单列表
     * @param $order_type
     * @param $page
     * @param $size
     * @param $list_type 订单入口：1 | 普通用户；2 | 商家用户
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function getDemandList($order_type, $page, $size, $list_type)
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        if ($shop_id) {
            if ($list_type == 2) {
                $list = self::getDemandListForShop($shop_id, $order_type, $page, $size);
                $list['count'] = self::getDemandOrderCountForShop();
                return $list;
            } else {
                $shop_id = 99999;
                $list = self::getDemandListForShop($shop_id, $order_type, $page, $size);
                $list['count'] = [
                    'service' => 0,
                    'shopConfirm' => 0,
                    'shopComplete' => 0
                ];
                return $list;
            }
        } else {
            $list = self::getDemandListForNormal($order_type, $page, $size);
            $list['count'] = self::getDemandOrderCountForNormal();
            return $list;
        }

    }

    /**
     * 获取服务订单列表
     * 普通用户 type: 已预约；待付款；待确认；待评价；已完成（1-5）
     * 店铺 type: 待确认；待服务；服务中；已完成(1-4)
     * @param $order_type
     * @param $page
     * @param $size
     * @param $list_type 订单入口：1 | 普通用户；2 | 商家用户
     * @return mixed
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function getServiceList($order_type, $page, $size, $list_type)
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        if ($shop_id) {
            if ($list_type == 2) {
                $list = self::getServiceListForShop($shop_id, $order_type, $page, $size);
                $list['count'] = self::getServiceOrderCountForShop();
                return $list;
            } else {
                $list = self::getServiceListForShop(999999, 1, $page, $size);
                $list['count'] = [
                    'shopConfirm' => 0,
                    'service' => 0,
                    'serviceIng' => 0,
                    'shopComplete' => 0
                ];
                return $list;
            }
        } else {

            $list = self::getServiceListForNormal($order_type, $page, $size);
            $list['count'] = self::getServiceOrderCountForNormal();
            return $list;
        }

    }

    /**
     * @param $params
     * @return array|int
     * @throws Exception
     */
    public static function saveComment($params)
    {

        Db::startTrans();
        try {
            $red_money = 0;
            if ($params['score_type'] == 1) {
                $red_money = self::checkCommentRed();
            }

            $imgs = $params['imgs'];
            unset($params['imgs']);
            $params['u_id'] = Token::getCurrentUid();
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
            if (strlen($imgs)) {
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
            }

            //修改评论状态
            $order_type = $params['order_type'];
            if ($order_type == CommonEnum::ORDER_IS_BOOKING) {
                $com_id = ServiceBookingT::update(['comment_id' => $obj->id], ['id' => $params['o_id']]);

            } else {
                $com_id = DemandOrderT::update(['comment_id' => $obj->id], ['id' => $params['o_id']]);
            }

            if (!$com_id) {
                Db::rollback();
                throw new OrderException(
                    ['code' => 401,
                        'msg' => '修改评论状态失败',
                        'errorCode' => 150011
                    ]
                );
            }

            Db::commit();
            return $red_money;
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    private static function checkCommentRed()
    {
        $count = OrderCommentT::where('u_id', Token::getCurrentUid())
            ->count();
        if (!$count) {
            return RedService::addRed(RedEnum::FIRST_PRAISE, Token::getCurrentUid());
        }
        return 0;
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

    /**
     * @param $shop_id
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
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

    /**
     *  已预约；待付款；待确认；待评价；已完成（1-5）
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    private static function getServiceListForNormal($order_type, $page, $size)
    {
        $u_id = Token::getCurrentUid();
        switch ($order_type) {
            case OrderEnum::SERVICE_NORMAL_BOOKING:
                return ServiceOrderV::bookingList($u_id, $page, $size);
                break;
            case OrderEnum::SERVICE_NORMAL_PAY:
                return ServiceOrderV::payList($u_id, $page, $size);
                break;
            case OrderEnum::SERVICE_NORMAL_CONFIRM:
                return ServiceOrderV::confirmList($u_id, $page, $size);
                break;
            case OrderEnum::SERVICE_NORMAL_COMMENT:
                return ServiceOrderV::commentList($u_id, $page, $size);
                break;
            case OrderEnum::SERVICE_NORMAL_COMPLETE:
                return ServiceOrderV::completeList($u_id, $page, $size);
                break;

        }

    }

    /**
     * 待确认；待服务；服务中；已完成(1-4)
     * @param $shop_id
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\Paginator
     */
    private static function getServiceListForShop($shop_id, $order_type, $page, $size)
    {

        switch ($order_type) {
            case OrderEnum::SERVICE_SHOP_CONFIRM:
                return ServiceOrderV::shopConfirm($shop_id, $page, $size);
                break;
            case OrderEnum::SERVICE_SHOP_BEGIN:
                return ServiceOrderV::service($shop_id, $page, $size);
                break;
            case OrderEnum::SERVICE_SHOP_ING:
                return ServiceOrderV::serviceIng($shop_id, $page, $size);
                break;
            case OrderEnum::SERVICE_SHOP_COMPLETE:
                return ServiceOrderV::shopComplete($shop_id, $page, $size);
                break;

        }


    }


    /**
     * 需求订单 ：店铺-点击去服务-需要检测用户是否已经支付
     * 服务订单 ：店铺-点击去服务-需要检测用户是否已经支付
     * @param $id
     * @param $type
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function checkOrderPay($id, $type)
    {
        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $order = DemandOrderT::where('id', $id)->field('pay_id')->find()->toArray();
        } else {
            $order = ServiceBookingT::where('id', $id)->field('pay_id')->find()->toArray();
        }
        $pay_id = $order['pay_id'];
        return $pay_id == CommonEnum::ORDER_STATE_INIT ? 2 : 1;

    }


    /**
     * 需求订单 ：用户-点击付款-需要检测商家有无选择已经电话联系
     * 服务订单：用户-点击付款-需要检测商家有无选择已经电话联系
     * @param $id
     * @param $type
     * @return array|int
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function checkPhone($id, $type)
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        $phone = 1;
        /* if (!$shop_id) {
             //普通用户
             if ($type == CommonEnum::ORDER_IS_DEMAND) {
                 $phone = DemandOrderT::where('id', $id)
                     ->field('phone_user')
                     ->find()->toArray();
             } else {
                 $phone = ServiceBookingT::where('id', $id)
                     ->field('phone_user')
                     ->find()->toArray();
             }
         }*/
        return $phone;

    }

    /**
     * 检测用户是否有未完成的订单
     * @param $shop_id
     * @return int
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function checkPhoneAccess($shop_id)
    {
        $u_id = Token::getCurrentUid();
        //检测用户是否有未完成的订单
        if (DemandOrderV::checkNoComplete($shop_id, $u_id)) {
            return 1;
        }
        if (ServiceOrderV::checkNoComplete($shop_id, $u_id)) {
            return 1;
        }
        return 2;

    }

    private static function checkShopBond($shop_id)
    {

        return true;

    }

    private static function checkShopFrozen($shop_id)
    {
        $frozen = ShopT::where('id', $shop_id)
            ->field('frozen')
            ->find();

        return !($frozen['frozen'] - 1);

    }

    public static function getUID($order_id, $type)
    {

        $info = OrderUserShopV::where('id', $order_id)
            ->where('type', $type)
            ->field('u_id')
            ->find();
        return $info['u_id'];
    }

    /**
     *  获取轮播信息
     * @param $province
     * @param $city
     * @param $area
     * @param $type
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ordersToBanner($province, $city, $area, $type)
    {
        $sql = preJoinSqlForGetDShops($province, $city, $area);
        $system = SystemDemandT::where('id', $type)->find();
        if ($system) {
            $time_begin = date('Y-m-d H:i', strtotime('-' . $system->count . ' day',
                time()));

        } else {
            $time_begin = date('Y-m-d H:i', strtotime('-' . 30 . ' day',
                time()));

        }
        if ($type == 3) {
            $list = OrderReportV::whereRaw($sql)
                ->whereTime('order_time', '>', $time_begin)
                ->field('user_name,source_name')
                ->order('order_time desc')
                ->limit(0, 30)
                ->select();
            return $list;


        } else {
            $list = OrderReportV::where('shop_type', $type)->whereRaw($sql)
                ->whereTime('order_time', '>', $time_begin)
                ->field('user_name,source_name')
                ->order('order_time desc')
                ->limit(0, 30)
                ->select();
            return $list;

        }

    }


    private static function getDemandOrderCountForNormal()
    {
        $u_id = Token::getCurrentUid();
        $count_arr = [
            'taking' => DemandOrderV::takingCount($u_id),
            'pay' => DemandOrderV::payCount($u_id),
            'confirm' => DemandOrderV::confirmCount($u_id),
            'comment' => DemandOrderV::commentCount($u_id),
            'complete' => DemandOrderV::completeCount($u_id)
        ];
        return $count_arr;

    }

    private static function getDemandOrderCountForShop()
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        $count_arr = [
            'service' => DemandOrderV::serviceCount($shop_id),
            'shopConfirm' => DemandOrderV::shopConfirmCount($shop_id),
            'shopComplete' => DemandOrderV::shopCompleteCount($shop_id)
        ];

        return $count_arr;

    }


    private static function getServiceOrderCountForNormal()
    {
        $u_id = Token::getCurrentUid();
        $count_arr = [
            'booking' => ServiceOrderV::bookingCount($u_id),
            'pay' => ServiceOrderV::payListCount($u_id),
            'confirm' => ServiceOrderV::confirmCount($u_id),
            'comment' => ServiceOrderV::commentListCount($u_id),
            'complete' => ServiceOrderV::completeListCount($u_id)
        ];

        return $count_arr;

    }

    private static function getServiceOrderCountForShop()
    {
        $shop_id = Token::getCurrentTokenVar('shop_id');
        $count_arr = [
            'shopConfirm' => ServiceOrderV::shopConfirmCount($shop_id),
            'service' => ServiceOrderV::serviceCount($shop_id),
            'serviceIng' => ServiceOrderV::serviceIngCount($shop_id),
            'shopComplete' => ServiceOrderV::shopCompleteCount($shop_id)
        ];

        return $count_arr;

    }


}