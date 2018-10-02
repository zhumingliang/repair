<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:18
 */

namespace app\api\model;


use think\Model;

class ImgT extends BaseModel
{
    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }


}