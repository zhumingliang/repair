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
    public function getHeadUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

}