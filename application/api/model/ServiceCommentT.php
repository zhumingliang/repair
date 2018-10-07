<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 1:59 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class ServiceCommentT extends Model
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
        $service = self::where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('s_id', '=', $service_id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->field('c_id,img_id');
                },
                'user' => function ($query) {
                    $query->field('id,nickName,avatarUrl');
                }
            ])
            ->order('create_time desc')
            ->hidden(['id', 'o_id', 'state', 'state', 'update_time', 's_id', 'type', 'score', 'u_id'])
            ->paginate($size, false, ['page' => $page]);
        return $service;

    }

}