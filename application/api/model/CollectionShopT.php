<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:32
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class CollectionShopT extends Model
{
    public function getHeadUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }


    public function shop()
    {
        return $this->belongsTo('ShopT',
            's_id', 'id');
    }
    public static function getList($page, $size)
    {
        $pagingData = self::with(['shop' => function ($query) {
            $query->field('id,head_url,name,address,phone');
        }])->where('state', '=',CommonEnum::STATE_IS_OK)
            ->where('u_id', '=',Token::getCurrentUid())
            ->field('id,s_id')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }

}