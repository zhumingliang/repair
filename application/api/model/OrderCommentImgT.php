<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/17
 * Time: 10:31 AM
 */

namespace app\api\model;


use think\Model;

class OrderCommentImgT extends Model
{


    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}