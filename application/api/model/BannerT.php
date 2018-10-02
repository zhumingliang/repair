<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午1:31
 */

namespace app\api\model;



class BannerT extends BaseModel
{
    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

}