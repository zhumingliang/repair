<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/26
 * Time: 下午6:32
 */

namespace app\api\model;


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
                , 'shop'=>function ($query) {
                    $query->field('id,address,phone');
                }
            ])
            ->field('id,shop_id,name,area,des,price,unit,des')
            ->find();
        return $service;

    }


}