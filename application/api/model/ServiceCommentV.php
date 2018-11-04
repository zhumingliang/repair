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
        return $this->hasMany('OrderCommentImgT',
            'o_id', 'id');
    }


    public static function getListForService($service_id, $page, $size)
    {
        $service = self::where('s_id', '=', $service_id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->field('o_id,img_id');
                }])
            ->order('create_time desc')
            ->hidden(['id', 'o_id', 'state', 'state', 'update_time', 's_id', 'type', 'score', 'u_id'])
            ->paginate($size, false, ['page' => $page]);
        return $service;

    }
}