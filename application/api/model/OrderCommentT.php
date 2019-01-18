<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/17
 * Time: 10:28 AM
 */

namespace app\api\model;


use think\Model;

class OrderCommentT extends Model
{

    public function imgs()
    {
        return $this->hasMany('OrderCommentImgT',
            'o_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo('ShopT',
            's_id', 'id');
    }

    public  static function getComment($id)
    {

        $comment = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->field('o_id,img_id');
                },
                'shop' => function ($query) {
                    $query->field('id,name');
                }
            ])
            ->find();

        return $comment;

    }

}