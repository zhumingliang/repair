<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/18
 * Time: 9:23 AM
 */

namespace app\api\model;


use think\Model;

class GoodsCommentImgT extends Model
{
    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}