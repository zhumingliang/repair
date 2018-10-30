<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/26
 * Time: 下午6:32
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;

class ServicesT extends BaseModel
{

    public function shop()
    {
        return $this->belongsTo('ShopT',
            'shop_id', 'id');
    }

    public function imgs()
    {
        return $this->hasMany('ServicesImgT',
            's_id', 'id');
    }



    /* public function getCoverAttr($value, $data)
     {
         return $this->prefixImgUrl($value, $data);
     }*/

    /**
     * 小程序获取服务信息
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getService($id)
    {
        $service = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
                , 'shop' => function ($query) {
                    $query->field('id,address,phone');
                }
            ])
            ->field('id,shop_id,name,area,des,price,unit,des')
            ->find();
        return $service;

    }


    /**
     * 1 | 价格由高到底，2 | 价格由低到高;5 | 综合
     * @param $type
     * @param $area
     * @param $key
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getServiceForPrice($type, $area, $key, $page, $size)
    {
        $order = 'create_time desc';
        if ($type == 2) {
            $order = 'price desc';
        } else if ($type == 3) {
            $order = 'price';
        }
        $pagingData = self::where('area', $area)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->field('id,cover,name,price')
            ->order($order)
            ->paginate($size, false, ['page' => $page]);
        return $pagingData;


    }

    public static function getServiceForCMS($id)
    {
        $service = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
                , 'shop' => function ($query) {
                    $query->field('id,address,phone,name as shop_name,city');
                }
            ])
            //->field('id,shop_id,name,area,des,price,unit,des')
            ->find();
        return $service;

    }


}