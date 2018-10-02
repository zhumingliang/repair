<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 上午1:02
 */

namespace app\api\model;


class GuidT extends BaseModel
{
    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

}