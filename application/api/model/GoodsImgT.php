<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:15 AM
 */

namespace app\api\model;


use think\Model;

class GoodsImgT extends Model
{
    protected $hidden=['create_time','update_time','state','g_id'];

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }


}