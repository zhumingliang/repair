<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:33
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use think\Model;

class CollectionServicesT extends Model
{
    public function service()
    {
        return $this->belongsTo('ServicesT',
            's_id', 'id');
    }

    public static function getList($page, $size)
    {
        $pagingData = self::with(['service' => function ($query) {
            $query->field('id,cover,name,price');
        }])->where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('u_id', '=', Token::getCurrentUid())
            ->field('id,s_id')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }


}