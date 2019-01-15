<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:14 AM
 */

namespace app\api\service;


use app\api\model\GoodsT;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class GoodsService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            $check = $this->checkGoodsName($params['name']);
            if ($check) {
                Db::rollback();
                throw  new OperationException([
                    'code' => 401,
                    'msg' => '商品已存在，不能重复新增',
                    'errorCode' => 160011
                ]);
            }

            $g_res = GoodsT::create($params);
            if (!$g_res) {
                Db::rollback();
                throw  new OperationException();
            }

            if (key_exists('banner', $params) && strlen($params['banner'])) {
                $relation = [
                    'name' => 'g_id',
                    'value' => $g_res->id
                ];
                $res = $this->saveImageRelation($params['banner'], $relation);
            }

            if (key_exists('show', $params) && strlen($params['show'])) {

            }


            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }


    private function checkGoodsName($name)
    {
        $count = GoodsT::where('name', $name)->count();
        return $count;

    }

    /**
     * 保存图片关联
     * @param $imgs
     * @param $relation
     * @param $table_name
     * @return bool
     * @throws \Exception
     */
    private static function saveImageRelation($imgs, $relation,$table_name)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $table = new $table_name();
        $res = $table->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }

}