<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-30
 * Time: 11:11
 */

namespace app\api\model;


use think\Model;

class ForumImgT extends Model
{
    protected $hidden=['create_time','update_time','state','f_id'];

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }

}