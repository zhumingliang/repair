<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/9
 * Time: 4:46 PM
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;

class CircleT extends BaseModel
{
    /* public function getHeadImgAttr($value, $data)
     {
         return $this->prefixImgUrl($value, $data);
     }*/

    public function category()
    {
        return $this->belongsTo('CircleCategoryT',
            'c_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo('AdminT',
            'u_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('CircleCommentT',
            'c_id', 'id');

    }

    public static function getListForCms($page, $size, $state)
    {
        $grade = Token::getCurrentTokenVar('grade');
        $pagingData = self::with(['category' => function ($query) {
            $query->field('id,name');
        }])->where('state', '=', $state)
            ->where(function ($query) use ($grade) {
                if ($grade == UserEnum::USER_GRADE_JOIN) {
                    $query->where('parent_id', '=', Token::getCurrentUid());
                } else if ($grade == UserEnum::USER_GRADE_JOIN) {
                    $query->where('parent_id', '=', Token::getCurrentTokenVar('parent_id'));
                }
            })
            ->hidden(['c_id', 'update_time', 'u_id', 'head_img', 'content', 'parent_id', 'province', 'area'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;

    }

    public static function getCirclesForAdmin($page, $size, $state)
    {
        $pagingData = self::with(['category' => function ($query) {
            $query->field('id,name');
        }])->where('state', '=', $state)
            ->hidden(['c_id', 'update_time', 'u_id', 'head_img', 'content', 'parent_id', 'province', 'area'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }

    public static function getCirclesForJoin($page, $size, $state, $province, $city, $area)
    {
        $sql = preJoinSqlForGetDShops($province, $city, $area);
        $pagingData = self::with(['category' => function ($query) {
            $query->field('id,name');
        }])->where('state', '=', $state)
            ->whereRaw($sql)
            ->hidden(['c_id', 'update_time', 'u_id', 'head_img', 'content', 'parent_id', 'province', 'area'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }

    public static function getCirclesForVillage($page, $size,$area)
    {
        $pagingData = self::with(['category' => function ($query) {
            $query->field('id,name');
        }])->where('state', '<', 4)
            ->where('parent_id', Token::getCurrentUid())
            ->hidden(['c_id', 'update_time', 'u_id', 'head_img', 'content', 'parent_id', 'province', 'area'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }

    public static function getListForMINI($page, $size, $province, $city, $area, $c_id)
    {
        $pagingData = self::where('state', '=', CommonEnum::PASS)
            ->where('c_id', '=', $c_id)
            ->whereRaw(preJoinSql($province, $city, $area))
            ->hidden(['c_id', 'update_time', 'u_id', 'parent_id', 'province', 'area', 'city', 'state', 'top', 'content'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;

    }

    /**
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCircle($id)
    {
        $circle = self::where('id', $id)
            ->with(['category' => function ($query) {
                $query->field('id,name');
            }, 'source' => function ($query) {
                $query->field('id,grade');
            }])
            ->hidden(['c_id', 'update_time', 'u_id', 'state', 'parent_id', 'top', 'province', 'area'])
            ->find();

        return $circle;
    }


    /**
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCircleForMINI($id)
    {
        $circle = self::where('id', $id)
            ->hidden([ 'city', 'c_id', 'update_time', 'u_id', 'state', 'parent_id', 'top', 'province', 'area'])
            ->find();

        return $circle;
    }


}