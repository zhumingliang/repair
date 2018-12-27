<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:27
 */

namespace app\api\model;


use think\Model;

class ShopImgT extends Model
{
    protected $hidden=['create_time','update_time','state','s_id'];

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }

}