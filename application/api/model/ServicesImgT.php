<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/26
 * Time: 下午10:27
 */

namespace app\api\model;


use think\Model;

class ServicesImgT extends Model
{
    protected $hidden=['id','create_time','update_time','state','s_id'];


    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }

}