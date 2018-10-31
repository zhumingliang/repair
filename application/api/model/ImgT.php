<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:18
 */

namespace app\api\model;

class ImgT extends BaseModel
{
    protected $hidden=['id','create_time','update_time','state'];

    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }


}