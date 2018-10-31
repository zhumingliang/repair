<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午9:46
 */

namespace app\api\service;


use app\api\model\ImgT;
use app\api\model\OrderListV;
use app\api\model\ShopStaffImgT;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use app\lib\exception\FaceException;
use app\lib\exception\ImageException;
use app\lib\exception\ParameterException;
use app\lib\exception\TokenException;
use think\Exception;

class ImageService
{

    const IMAGE_NORMAL = 1;

    const IMAGE_STAFF_UP = 2;

    const IMAGE_STAFF_SEARCH = 3;

    /**
     * 组装图片关联表
     * @param $imgs
     * @param $relation
     * @return array
     */
    public static function ImageHandel($imgs, $relation)
    {
        $img_arr = explode(',', $imgs);
        $arr = array();
        if (count($img_arr)) {
            foreach ($img_arr as $v) {
                $item = [
                    'img_id' => $v,
                    $relation['name'] => $relation['value'],
                    'state' => CommonEnum::STATE_IS_OK

                ];
                array_push($arr, $item);

            }
        }
        return $arr;
    }

    public static function getImageUrl($id)
    {
        $img = ImgT::where('id', $id)
            ->find();
        return $img->url;

    }


    public static function saveImageFromWX($file)
    {
        $img = self::imageSave($file);
        return $img['id'];
    }

    /**
     * 保存图片信息
     * @param $file
     * @return array
     * @throws ImageException
     */
    private static function imageSave($file)
    {
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/static/imgs';
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        $info = $file->move($path);
        if ($info) {
            $img = ImgT::create(
                [
                    'url' => 'static/imgs' . '/' . $info->getSaveName(),
                    'state' => CommonEnum::STATE_IS_OK]
            );
            if (!$img) {
                throw new ImageException();
            }
            return [
                'id' => $img->id,
                'url' => config('setting.img_prefix') . 'static/imgs' . '/' . $info->getSaveName()
            ];

        } else {
            throw new ImageException();
        }
    }


    /**
     * 根据人脸识别店铺订单
     * @param $file
     * @param $group_ids
     * @return array
     * @throws FaceException
     * @throws ImageException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function staffSearch($file, $group_ids)
    {
        $img = self::imageSave($file);
        $url = $img['url'];
        //先检测图片是否合法
        $face = FaceService::instance();
        $face->detectFace($url);
        $face_token = $face->searchFace($group_ids, $url);
        //获取店铺信息
        $shop = ShopStaffImgT::where('face_token', $face_token)
            ->field('s_id')
            ->find();
        $shop_id = $shop['s_id'];
        //获取该店铺订单列表
        $orders = self::getOrderForShop($shop_id);
        $shop_info = ShopT::where('id', $shop_id)->field('name,area,address,phone')->find();
        return ['orders' => $orders, 'shop_info' => $shop_info];


    }

    /**
     * 获取店铺此时正在服务中服务订单和需求订单
     * @param $shop_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getOrderForShop($shop_id)
    {
        $list = OrderListV::where('shop_id', $shop_id)
            ->select();
        return $list;

    }


}