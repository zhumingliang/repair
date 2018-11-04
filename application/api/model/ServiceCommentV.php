<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/5
 * Time: 12:03 AM
 */

namespace app\api\model;


use think\Model;

class ServiceCommentV extends Model
{

    public function imgs()
    {
        return $this->hasMany('ServiceCommentImgT',
            'c_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('UserT',
            'u_id', 'id');
    }

    public static function getListForService($service_id, $page, $size)
    {
        $service = self::where('s_id', '=', $service_id)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $service;

    }
}