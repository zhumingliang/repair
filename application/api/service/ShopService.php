<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:25
 */

namespace app\api\service;


use app\api\model\BondBalanceV;
use app\api\model\CityDiscountT;
use app\api\model\ExtendMoneyV;
use app\api\model\ServiceBookingT;
use app\api\model\ServiceExtendT;
use app\api\model\ServicesImgT;
use app\api\model\ServicesT;
use app\api\model\ShopImgT;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use app\lib\exception\ShopException;
use think\Db;
use think\Exception;

class ShopService
{
    const SERVICE_EXTEND = 1;
    const SERVICE_EXTEND_READY = 1;
    const SERVICE_EXTEND_PASS = 2;
    const SERVICE_EXTEND_REFUSE = 3;

    /**
     * 保存成为商铺申请
     * @param $params
     * @throws Exception
     */
    public static function apply($params)
    {
        Db::startTrans();
        try {
            $imgs = $params['imgs'];
            $params['head_url'] = base64toImg($params['head_url']);
            unset($params['imgs']);
            $obj = ShopT::create($params);
            if (!$obj) {
                throw new ShopException();
            }
            $relation = [
                'name' => 's_id',
                'value' => $obj->id
            ];
            $res = self::saveImageRelation($imgs, $relation);
            if (!$res) {
                Db::rollback();
                throw new ShopException(
                    ['code' => 401,
                        'msg' => '创建商铺申请图片关联失败',
                        'errorCode' => 60002
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
     * 保存申请和图片关联
     * @param $imgs
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    private static function saveImageRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $demandImgT = new ShopImgT();
        $res = $demandImgT->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }


    /**
     *  新增商铺服务
     * @param $params
     * @throws Exception
     */
    public static function addService($params)
    {
        Db::startTrans();
        try {
            $params['cover'] = base64toImg($params['cover']);
            $extend = $params['extend'];
            $imgs = $params['imgs'];
            $obj = ServicesT::create($params);
            $s_id = $obj->id;
            if (!$obj) {
                Db::rollback();
                throw new ShopException([
                    ['code' => 401,
                        'msg' => '新增商铺服务失败',
                        'errorCode' => 60004
                    ]
                ]);
            }
            //处理服务图片
            if (strlen($imgs)) {

                if (!self::addServiceImage($imgs, $s_id)) {

                    Db::rollback();
                    throw new ShopException([
                        ['code' => 401,
                            'msg' => '新增服务图片关联',
                            'errorCode' => 60006
                        ]
                    ]);
                }

            }
            //商品需要推广
            if ($extend == self::SERVICE_EXTEND) {
                $obj_extend = self::addExtend($s_id);
                if (!$obj_extend) {
                    Db::rollback();
                    throw new ShopException([
                        ['code' => 401,
                            'msg' => '新增推广申请失败',
                            'errorCode' => 60005
                        ]
                    ]);
                }

            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

    }


    /**
     * 新增服务推广记录
     * @param $s_id
     * @return ServiceExtendT
     */
    private static function addExtend($s_id)
    {
        $data = [
            's_id' => $s_id,
            'state' => self::SERVICE_EXTEND_READY
        ];
        $obj_extend = ServiceExtendT::create($data);

        return $obj_extend;

    }


    /**
     * 新增服务图片关联
     * @param $imgs
     * @param $s_id
     * @return bool
     * @throws \Exception
     */
    private static function addServiceImage($imgs, $s_id)
    {
        $imgs_arr = explode(',', $imgs);
        $list_arr = [];
        foreach ($imgs_arr as $k => $v) {

            $list = [
                's_id' => $s_id,
                'img_id' => $v,
                'state' => CommonEnum::STATE_IS_OK
            ];
            array_push($list_arr, $list);
        }
        $servicesImage = new ServicesImgT();
        $res = $servicesImage->saveAll($list_arr);
        if (!$res) {
            return false;
        }
        return true;

    }

    /**
     * 检查客户保证金是否充足
     * @param $money
     * @return array
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function checkBalance($money)
    {
        $balance = BondBalanceV::where('u_id', Token::getCurrentUid())
            ->sum('money');
        $need = $money <= 1000 ? 500 : $money / 2;
        if ($balance >= $need) {
            return [
                'res' => true
            ];
        } else {
            return [
                'res' => false,
                'money' => $need - $balance
            ];
        }


    }


    private static function checkShopStatus()
    {
        return true;

    }

    /**
     * 保存预约订单
     * @param $params
     * @return array
     * @throws Exception
     * @throws ShopException
     * @throws \app\lib\exception\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function booking($params)
    {

        $init_state = CommonEnum::ORDER_STATE_INIT;
        $openid = Token::getCurrentOpenid();
        $money = self::getServiceMoney($params['s_id']);
        $params['openid'] = $openid;
        $params['orderNumber'] = makeOrderNo();
        $params['money'] = $money;
        $params['pay_id'] = $init_state;
        $params['refund_id'] = $init_state;
        $params['comment_id'] = $init_state;
        $params['confirm_id'] = $init_state;
        $params['r_id'] = $init_state;
        $booking = ServiceBookingT::create($params);
        if (!$booking) {
            throw new ShopException(
                ['code' => 401,
                    'msg' => '预约服务下单失败',
                    'errorCode' => 60007
                ]
            );
        }
        return [
            'id' => $booking->id,
            'money' => $money
        ];
    }

    /**
     *  获取服务金额
     * @param $id
     * @return float|int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getServiceMoney($id)
    {
        $service = ExtendMoneyV::get($id);
        if ($service->state != self::SERVICE_EXTEND_PASS) {
            return $service->price;
        }

        $discount = CityDiscountT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('city', '=', $service->city)
            ->whereOr('type', '=', 1)
            ->select();

        if (!$discount) {
            return $service->price;
        }


        $city_dis = 0;
        $plate_dis = 0;
        foreach ($discount as $k => $v) {
            if ($v->city == $service->city) {
                $city_dis = $v->discount;
            } else {
                $plate_dis = $v->discount;
            }
        }
        $dis = $city_dis ? $city_dis : $plate_dis;
        return ($service->price) * (1 - $dis / 100);

    }
}