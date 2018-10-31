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

    protected $hidden = ['create_time', 'update_time', 's_id'];


    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo('ShopT',
            's_id', 'id');
    }


    public static function examineInfo($id)
    {
        $info = self::where('id', $id)
            ->with(['imgUrl', 'shop'])
            ->find();
        return $info;

    }
}