<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-30
 * Time: 11:10
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class ForumT extends Model
{
    public function user()
    {
        return $this->belongsTo('UserT',
            'u_id', 'id');
    }

    public function imgs()
    {
        return $this->hasMany('ForumImgT',
            'f_id', 'id');
    }

    public static function getForumForCMS($id)
    {
        $forum = self::where('id', $id)
            ->with(
                [
                    'user' => function ($query) {
                        $query->field('id,nickName,avatarUrl,name_sub,phone');
                    },
                    'imgs' => function ($query) {
                        $query->with(['imgUrl'])
                            ->where('state', '=', CommonEnum::STATE_IS_OK);
                    }
                ])
            ->hidden(['f_id', 'update_time', 'u_id'])
            ->find();

        return $forum;

    }
    public static function getListForSelf($u_id, $page, $size)
    {
        $forums = self::where('u_id', $u_id)
            ->where('state', '<', 4)
            ->field('id,create_time,title,content')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $forums;

    }


}