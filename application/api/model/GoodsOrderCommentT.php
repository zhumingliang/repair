<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:17 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class GoodsOrderCommentT extends Model
{
    public function imgs()
    {
        return $this->hasMany('GoodsCommentImgT',
            'c_id', 'id');
    }


    public  static function getComment($id)
    {

        $comment = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state','=',CommonEnum::STATE_IS_OK)
                        ->field('c_id,img_id');
                }
            ])
            ->find();

        return $comment;

    }


}