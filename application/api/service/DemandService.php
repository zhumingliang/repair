<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午9:41
 */

namespace app\api\service;


use app\api\model\DemandImgT;
use app\api\model\DemandT;
use app\api\model\DemandV;
use app\api\model\UserT;
use app\lib\exception\DemandException;
use think\Db;
use think\Exception;

class DemandService
{
    /**
     * 保存需求
     * @param $params
     * @throws Exception
     */
    public static function save($params)
    {

        Db::startTrans();
        try {
            $imgs = $params['imgs'];
            if (strlen($imgs)) {
                $imgs_arr = explode(',', $imgs);
                $params['cover'] = ImageService::getImageUrl($imgs_arr[0]);
            }

            unset($params['imgs']);
            $obj = DemandT::create($params);
            if (!$obj) {
                throw new DemandException();
            }
            $relation = [
                'name' => 'd_id',
                'value' => $obj->id
            ];
            $res = self::saveImageRelation($imgs, $relation);
            if (!$res) {
                Db::rollback();
                throw new DemandException(
                    ['code' => 401,
                        'msg' => '创建需求图片关联失败',
                        'errorCode' => 50002
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
     * 保存需求和图片关联
     * @param $imgs
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    private static function saveImageRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $demandImgT = new DemandImgT();
        $res = $demandImgT->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }


    public static function getList($params)
    {
        $list = DemandV::getList($params['province'], $params['city'], $params['area'], $params['page'], $params['size']);
        $list['data'] = self::preListData($list['data'], $params['latitude'], $params['longitude']);
        $list['grade'] = self::getuserGrade();
        return $list;

    }


    private static function preListData($list, $lat, $lng)
    {
        if (!count($list)) {
            return $list;
        }
        foreach ($list as $k => $v) {
            $lat1 = $v['latitude'];
            $ln1 = $v['longitude'];
            $list[$k]['distance'] = self::getDistance($lat1, $ln1, $lat, $lng);
        }

        return $list;

    }


    /**
     *计算两经纬度之间的距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @param float $radius
     * @return float
     */
    private static function getDistance($lat1, $lng1, $lat2, $lng2, $radius = 6378.137)
    {
        $rad = floatval(M_PI / 180.0);
        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lng1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lng2) * $rad;
        $theta = $lon2 - $lon1;
        $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));
        if ($dist < 0) {
            $dist += M_PI;
        }
        $dist = $dist * $radius;
        return round($dist, 1);
    }

    private static function getuserGrade()
    {
        $u_id = Token::getCurrentUid();
        $user = UserT::with('shop')->where('id', $u_id)->find();
        if (isset($user->shop) && ($user->shop->state == 2)) {
            return 2;
        }
        return 1;
    }
}