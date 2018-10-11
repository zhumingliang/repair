<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午9:46
 */

namespace app\api\service;


use app\lib\enum\CommonEnum;
use think\Exception;

class ImageService
{


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


    public static function uploadImg($file)
    {
        var_dump($file);
        try{
            $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/static/imgs';
            if (!is_dir($path)) {
                mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
            }
            $name = guid();
            $imgUrl = $path . '/' . $name . '.jpg';
            $a = file_put_contents($imgUrl, $file);

            $imgUrl2 = 'static/imgs/' . $name . '.jpg';
            return $a ? $imgUrl2 : '';
        }catch (Exception $e){
            return $e->getMessage();
        }



    }


}