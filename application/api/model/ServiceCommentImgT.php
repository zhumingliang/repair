<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 11:30 PM
 */

namespace app\api\model;



class ServiceCommentImgT extends BaseModel
{

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}