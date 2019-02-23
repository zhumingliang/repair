<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-02-15
 * Time: 22:48
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class ForumListV extends Model
{
    public function imgs()
    {
        return $this->hasMany('ForumImgT',
            'f_id', 'id');
    }

    public static function getListForSelf($u_id, $page, $size)
    {
        $forums = self::where('u_id', $u_id)
            ->where('state', '<', 4)
            ->with(
                [
                    'imgs' => function ($query) {
                        $query->with(['imgUrl'])
                            ->where('state', '=', CommonEnum::STATE_IS_OK);
                    }
                ])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $forums;

    }

    public static function getListForAll($page, $size)
    {
        $forums = self::where('state', '=', 2)
            ->with(
                [
                    'imgs' => function ($query) {
                        $query->with(['imgUrl'])
                            ->where('state', '=', CommonEnum::STATE_IS_OK);
                    }
                ])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $forums;

    }

}