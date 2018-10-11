<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:28
 */

namespace app\api\model;


class ShopT extends BaseModel
{

    public function imgs()
    {
        return $this->hasMany('ShopImgT',
            's_id', 'id');
    }

    public function getHeadUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    public static function getShopInfo($u_id)
    {
        $info = self::where('u_id', '=', $u_id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->hidden(['u_id', 'create_time', 'update_time', 'frozen'])
            ->find();
        return $info;

    }

}