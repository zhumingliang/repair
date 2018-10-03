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

    public function getCoverAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }



}