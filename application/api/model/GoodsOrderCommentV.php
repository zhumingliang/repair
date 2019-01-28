<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-28
 * Time: 23:50
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class GoodsOrderCommentV extends Model
{
    public function imgs()
    {
        return $this->hasMany('GoodsCommentImgT',
            'c_id', 'id');
    }


    public  static function getComment($id,$page,$size)
    {

        $comment = self::where('g_id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state','=',CommonEnum::STATE_IS_OK)
                        ->field('c_id,img_id');
                }
            ])
            ->paginate($size, false, ['page' => $page]);
        return $comment;

    }



}