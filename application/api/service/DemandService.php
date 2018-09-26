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


}