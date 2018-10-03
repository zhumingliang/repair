<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:28
 */

namespace app\api\model;


use think\Model;

class ShopT extends Model
{
    public function getHeadUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

}