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
use app\api\model\CollectionServicesT;
use app\api\model\CollectionShopT;
use app\api\model\ExtendMoneyV;
use app\api\model\OrderCommentT;
use app\api\model\ServiceBookingT;
use app\api\model\ServiceExtendT;
use app\api\model\ServiceListV;
use app\api\model\ServicesImgT;
use app\api\model\ServicesT;
use app\api\model\ShopImgT;
use app\api\model\ShopListV;
use app\api\model\ShopStaffImgT;
use app\api\model\ShopT;
use app\api\validate\TokenGet;
use app\lib\enum\CommonEnum;
use app\lib\exception\FaceException;
use app\lib\exception\ShopException;
use think\Db;
use think\Exception;

class ShopService
{
    const SERVICE_EXTEND = 1;
    const SERVICE_EXTEND_READY = 1;
    const SERVICE_EXTEND_PASS = 2;
    const SERVICE_EXTEND_REFUSE = 3;
    const SEARCH_SHOP = 1;
    const SEARCH_SERVICE = 2;

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
            $params['head_url'] = ImageService::getImageUrl($params['head_url']);
            unset($params['imgs']);

            if (self::checkShopName($params['name'])) {
                throw new ShopException(
                    ['code' => 401,
                        'msg' => '店铺名称已存在，请重新输入',
                        'errorCode' => 60003
                    ]
                );
            }
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
     * @param $name
     * @return float|string
     */
    public static function checkShopName($name)
    {
        $count = ShopT::where('name', $name)
            ->where('state', '<>', CommonEnum::DELETE)
            ->count();

        return $count;

    }

    /**
     * 修改店铺信息
     * @param $params
     * @throws Exception
     */
    public static function updateShop($params)
    {

        Db::startTrans();
        try {
            $params['id'] = Token::getCurrentTokenVar('shop_id');
            if (isset($params['head_url'])) {
                $params['head_url'] = ImageService::getImageUrl($params['head_url']);

            }

            if (isset($params['staffs']) && strlen($params['staffs'])) {
                $staffs = $params['staffs'];
                unset($params['staffs']);
                $relation = [
                    'name' => 's_id',
                    'value' => $params['id']
                ];
                $staff_res = self::saveStaffRelation($staffs, $relation);
                if (!$staff_res) {
                    Db::rollback();
                    throw new ShopException(
                        ['code' => 401,
                            'msg' => '创建商铺员工头像关联失败',
                            'errorCode' => 600011
                        ]
                    );
                }

            }
            if (isset($params['imgs']) && strlen($params['imgs'])) {
                $imgs = $params['imgs'];
                unset($params['imgs']);
                $relation = [
                    'name' => 's_id',
                    'value' => $params['id']
                ];
                $imgs_res = self::saveImageRelation($imgs, $relation);
                if (!$imgs_res) {
                    Db::rollback();
                    throw new ShopException(
                        ['code' => 401,
                            'msg' => '创建商铺申请图片关联失败',
                            'errorCode' => 60002
                        ]
                    );
                }

            }


            $res = ShopT::update($params, ['id' => $params['id']]);
            if (!$res) {
                Db::rollback();
                throw new ShopException(
                    ['code' => 401,
                        'msg' => '店铺修改失败',
                        'errorCode' => 600012
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

    private static function saveStaffRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $staffImgT = new ShopStaffImgT();
        $res = $staffImgT->saveAll($data);
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
            $params['cover'] = ImageService::getImageUrl($params['cover']);
            $params['state'] = CommonEnum::STATE_IS_OK;
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
            if (strlen($imgs) > 0) {

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
        $params['order_number'] = makeOrderNo();
        $params['origin_money'] = $money;
        $params['update_money'] = $money;
        $params['pay_id'] = $init_state;
        $params['refund_id'] = $init_state;
        $params['comment_id'] = $init_state;
        $params['comment_id'] = $init_state;
        $params['confirm_id'] = $init_state;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['phone_user'] = CommonEnum::STATE_IS_OK;
        $params['phone_shop'] = CommonEnum::STATE_IS_OK;
        $params['shop_confirm'] = CommonEnum::STATE_IS_FAIL;
        $params['service_begin'] = CommonEnum::STATE_IS_FAIL;
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

        $shop_id = self::getShopID($params['s_id']);
        //添加服务记录
        OrderMsgService::saveShop($shop_id, $booking->id, 2, 1);

        //发送消息通知商家
        (new SendMsgService($booking->id, $shop_id))->sendToShop();

        return [
            'id' => $booking->id,
            'money' => $money
        ];
    }


    private static function getShopID($s_id)
    {
        $info = ServicesT::where('id', $s_id)->field('shop_id')->find();
        return $info->shop_id;

    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getServiceMoney($id)
    {
        $extend = ExtendService::getExtendPrice($id);

        if ($extend['extend'] == 2) {
            $service_ino = ServicesT::where('id', $id)->find();
            return $service_ino->price;

        }
        return ($extend['extend_price']) * 100;


    }

    /**
     * 获取店铺信息-审核状态
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function getShopInfo()
    {
        $u_id = Token::getCurrentUid();
        $info = ShopT::getShopInfo($u_id);
        return $info;

    }

    public static function getShopInfoForCms($id)
    {
        $info = ShopT::getShopInfoForCMS($id);
        $info['bond_balance'] = WithDrawService::getBondBalance($info->u_id);
        return $info;

    }


    /**
     * * 获取店铺信息-编辑
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function getInfoForEdit()
    {
        $u_id = Token::getCurrentUid();
        $info = ShopT::getShopInfoForEdit($u_id);
        return $info;
    }

    /**
     * 对商铺图片进行审核通过
     * 并将数据保存到百度云人脸库
     * @param $shop_img_id
     * @param $url
     * @param $city
     * @throws Exception
     */
    public static function examineStaff($shop_img_id)
    {

        Db::startTrans();
        try {

            //获取审核信息
            $info = self::getExamineInfo($shop_img_id);
            $url = $info['url'];
            $city = $info['city'];
            //检测图片是否合格
            $face = FaceService::instance();
            if (!$face->detectFace($url)) {
                Db::rollback();
                throw  new FaceException();
            }
            //添加图片到百度人脸库--检测合格
            $groupId = md5($city);
            $register_res = $face->register($url, $groupId, $shop_img_id);
            if (!$register_res['res']) {
                throw  new FaceException(
                    ['code' => 401,
                        'msg' => '上传图片到百度云人脸库失败',
                        'errorCode' => 99004
                    ]
                );
            }
            //将face_token关联到ShopStaffImgT
            //修改店铺关联状态
            $res = ShopStaffImgT::update(
                [
                    'state' => CommonEnum::PASS,
                    'face_token' => $register_res['face_token']
                ],
                ['id' => $shop_img_id]);
            if (!$res) {
                Db::rollback();
                throw new ShopException(
                    ['code' => 401,
                        'msg' => '图片状态修改失败',
                        'errorCode' => 50009
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
     * 对商铺图片进行删除处理
     * 并将数据从百度云人脸库删除
     * @param $shop_img_id
     * @param $face_token
     * @param $city
     * @throws Exception
     */
    public static function deleteStaff($shop_img_id, $city, $face_token)
    {

        Db::startTrans();
        try {
            //修改店铺关联状态
            $res = ShopStaffImgT::update(
                [
                    'state' => CommonEnum::DELETE,
                ],
                ['id' => $shop_img_id]);
            if (!$res) {
                Db::rollback();
                throw new ShopException(
                    ['code' => 401,
                        'msg' => '图片状删除改失败',
                        'errorCode' => 50010
                    ]
                );
            }
            if ($face_token) {
                $face = FaceService::instance();
                if (!$face->deleteFace($shop_img_id, $city, $face_token)) {
                    Db::rollback();
                    throw  new FaceException([
                        ['code' => 401,
                            'msg' => '删除人脸库数据失败',
                            'errorCode' => 50013
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
     * 用户进入店铺 获取店铺信息
     * @param $id
     * @return array
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     */
    public static function getInfoForNormal($id)
    {

        $info = ShopT::getShopInfoForNormal($id);

        $comment_count = OrderCommentT::where('s_id', $id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->count();


        $collection = CollectionShopT::where('u_id', Token::getCurrentUid())
            ->where('s_id', $id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->find();
        $collection_id = 0;
        if ($collection) {
            $collection_id = $collection->id;
        }


        return [
            'info' => $info,
            'comment_count' => $comment_count,
            'score' => self::getShopScore($id),
            'collection' => $collection_id,
            'phone_check' => OrderService::checkPhoneAccess($id)
        ];
    }

    public static function getShopScore($shop_id)
    {
        $comment_score = OrderCommentT::where('id', $shop_id)
            ->avg('score');
        $score = $comment_score ? $comment_score : 5;
        return $score;
    }

    /**
     * @param $search_type 1 | 店铺；2 | 服务
     * @param $type 1 | 综合，2 |价格由高到底 3 | 价格由低到高，4| 销售量，5 | 销售量由低到高，6 | 销售量由高到底
     * @param $area
     * @param $key
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getListIndex($search_type, $type, $area, $key, $page, $size)
    {
        $list = array();
        if ($search_type == self::SEARCH_SHOP) {
            $list = ShopListV::getList($type, $area, $key, $page, $size);
            $data = $list['data'];
            $list['data'] = self::shopHeader($data);

        } else if ($search_type == self::SEARCH_SERVICE) {
            if ($type < 4) {
                $list = ServicesT::getServiceForPrice($type, $area, $key, $page, $size);

            } else {
                $list = ServiceListV::getListForSell($type, $area, $key, $page, $size);
            }

        }

        return $list;
    }


    private static function shopHeader($list)
    {
        if (count($list)) {
            foreach ($list as $k => $v) {
                $imgs = $v['imgs'][0];
                $head = $imgs['img_url']['url'];
                $list[$k]['cover'] = $head;
            }
        }

        return $list;

    }

    private static function getExamineInfo($id)
    {

        $info = ShopStaffImgT::examineInfo($id);

        return [
            'url' => $info->imgUrl->url,
            'city' => $info->shop->city
        ];

    }
}