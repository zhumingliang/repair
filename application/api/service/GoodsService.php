<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:14 AM
 */

namespace app\api\service;


use app\api\model\GoodsFormatT;
use app\api\model\GoodsImgT;
use app\api\model\GoodsT;
use app\api\model\GoodsV;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class GoodsService
{
    /**
     * 保存商品
     * @param $params
     * @throws Exception
     */
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
            $params['state'] = CommonEnum::STATE_IS_OK;
            if (key_exists('cover', $params) && strlen($params['cover'])) {
                $params['cover'] = ImageService::getImageUrl($params['cover']);
            }
            $g_res = GoodsT::create($params);
            if (!$g_res) {
                Db::rollback();
                throw  new OperationException();
            }

            //处理商品图片
            $img_arr = array();
            if (key_exists('banner', $params) && strlen($params['banner'])) {
                $img_arr = $this->preImage($img_arr, $params['banner'], $g_res->id, 1);
            }

            if (key_exists('show', $params) && strlen($params['show'])) {
                $img_arr = $this->preImage($img_arr, $params['show'], $g_res->id, 2);
            }

            if (count($img_arr)) {
                $goodsImage = new GoodsImgT();
                $res = $goodsImage->saveAll($img_arr);
                if (!$res) {
                    Db::rollback();
                    throw  new OperationException([
                        'code' => 401,
                        'msg' => '新增商品失败，图片处理失败',
                        'errorCode' => 160011
                    ]);
                }
            }

            if (key_exists('format', $params) && strlen($params['format'])) {
                $format_res = $this->saveFormat($params['format'], $g_res->id);
                if (!$format_res) {
                    Db::rollback();
                    throw  new OperationException([
                        'code' => 401,
                        'msg' => '新增商品失败，商品详情保存失败',
                        'errorCode' => 160011
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
     * 修改操作
     * @param $params
     * @throws Exception
     */
    public function update($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            if (key_exists('cover', $params) && strlen($params['cover'])) {
                $params['cover'] = ImageService::getImageUrl($params['cover']);
            }
            $g_res = GoodsT::update($params, ['id' => $id]);
            if (!$g_res) {
                Db::rollback();
                throw  new OperationException([
                    'code' => 401,
                    'msg' => '修改操作失败',
                    'errorCode' => 160011
                ]);
            }

            //处理商品图片
            $img_arr = array();
            if (key_exists('banner', $params) && strlen($params['banner'])) {
                $img_arr = $this->preImage($img_arr, $params['banner'], $id, 1);
            }

            if (key_exists('show', $params) && strlen($params['show'])) {
                $img_arr = $this->preImage($img_arr, $params['show'], $id, 2);
            }

            if (count($img_arr)) {
                $goodsImage = new GoodsImgT();
                $res = $goodsImage->saveAll($img_arr);
                if (!$res) {
                    Db::rollback();
                    throw  new OperationException([
                        'code' => 401,
                        'msg' => '修改商品失败，图片处理失败',
                        'errorCode' => 160011
                    ]);
                }
            }

            if (key_exists('format', $params) && strlen($params['format'])) {
                $format_res = $this->saveFormat($params['format'], $id);
                if (!$format_res) {
                    Db::rollback();
                    throw  new OperationException([
                        'code' => 401,
                        'msg' => '修改商品失败，商品详情保存失败',
                        'errorCode' => 160011
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
     * 检查新增商品是否有重名
     * @param $name
     * @return float|string
     */
    private function checkGoodsName($name)
    {
        $count = GoodsT::where('name', $name)->count();
        return $count;

    }

    /**
     * 处理图片
     * @param $return_arr
     * @param $img
     * @param $g_id
     * @param $type
     * @return mixed
     */
    private function preImage($return_arr, $img, $g_id, $type)
    {
        $img_arr = explode(',', $img);
        foreach ($img_arr as $k => $v) {
            $list = [
                'g_id' => $g_id,
                'type' => $type,
                'img_id' => $v,
                'state' => CommonEnum::STATE_IS_OK,

            ];

            array_push($return_arr, $list);
        }
        return $return_arr;

    }

    /**
     * @param $format
     * @param $g_id
     * @return \think\Collection
     * @throws \Exception
     */
    private function saveFormat($format, $g_id)
    {
        $format_arr = explode(';', $format);
        $data_arr = array();
        foreach ($format_arr as $k => $v) {
            $value_arr = explode(',', $v);
            $list = [
                'g_id' => $g_id,
                'state' => CommonEnum::STATE_IS_OK,
                'name' => $value_arr[0],
                'detail' => $value_arr[1],
            ];
            array_push($data_arr, $list);

        }

        $goodsFormat = new GoodsFormatT();
        return $goodsFormat->saveAll($data_arr);
    }

    public function getGoods($id)
    {
        $info = GoodsT::getInfo($id);
        $info['sell_num'] = GoodsV::getGoodsSell($id);
        return $info;
    }


}