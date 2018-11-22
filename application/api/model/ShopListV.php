<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/20
 * Time: 10:20 AM
 */

namespace app\api\model;


use think\Model;

class ShopListV extends Model
{

    public function imgs()
    {
        return $this->hasMany('ShopImgT',
            's_id', 'id');
    }

    public static function getList($type, $area, $key, $page, $size)
    {
        $order = 'shop_id';
        if ($type == 2 || $type == 5) {
            $order = 'sell_money desc';
        } else if ($type == 3 || $type == 6) {
            $order = 'sell_money';
        }
        $pagingData = self::where('area', $area)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->field('shop_id as id,cover,name,sell_money as price')
            ->order($order)
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }

}