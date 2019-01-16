<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:15 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class GoodsT extends Model
{
    public function imgs()
    {
        return $this->hasMany('GoodsImgT',
            'g_id', 'id');
    }

    public function format()
    {
        return $this->hasMany('GoodsFormatT',
            'g_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('GoodsCategoryT',
            'c_id', 'id');
    }


    public static function getInfo($id)
    {
        $info = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', CommonEnum::STATE_IS_OK);
                }
                ,
                'format' => function ($query) {
                    $query->where('state', '=', CommonEnum::STATE_IS_OK);
                },
                'category' => function ($query) {
                    $query->where('state', '=', CommonEnum::STATE_IS_OK);
                }
            ])
            ->hidden(['update_time'])
            ->find();
        return $info;

    }

    public static function getListForMINI($page, $size)
    {
        $list = self::where('state', '=', CommonEnum::PASS)
            ->field('id,name,cover,round(money/100,2) as money,score')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;

    }
}