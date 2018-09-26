<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:25
 */

namespace app\api\service;


use app\api\model\ShopImgT;
use app\api\model\ShopT;
use app\lib\exception\ShopException;
use think\Db;
use think\Exception;

class ShopService
{
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


    public function addService($params)
    {


    }

}