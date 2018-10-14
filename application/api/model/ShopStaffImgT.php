<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/14
 * Time: 11:33 PM
 */

namespace app\api\model;



class ShopStaffImgT extends BaseModel
{

    protected $hidden = [ 'create_time', 'update_time', 's_id'];

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}