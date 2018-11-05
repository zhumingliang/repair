<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/5/27
 * Time: 下午4:06
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use think\Model;

class AdminT extends Model
{
    public function adminJoin()
    {
        return $this->hasOne('AdminJoinT',
            'admin_id', 'id');
    }

    public static function getVillagesForAdmin($page, $size, $key)
    {
        $pagingData = self::where('state', CommonEnum::STATE_IS_OK)
            ->where('grade', UserEnum::USER_GRADE_VILLAGE)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('username', 'like', '%' . $key . '%');
                }
            })
            ->hidden(['state', 'create_time', 'pwd', 'parent_id', 'grade'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }

    public static function getVillagesForJoin($page, $size, $key)
    {
        $sql_join = preJoinSqlForGetDShops(Token::getCurrentTokenVar('province'), Token::getCurrentTokenVar('city'),
            Token::getCurrentTokenVar('area'));

        $pagingData = VillageV::whereRaw($sql_join)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('username', 'like', '%' . $key . '%');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }

    public static function getJoins($page, $size, $key)
    {
        $pagingData = JoinV::where('state', '<', CommonEnum::DELETE)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('username', 'like', '%' . $key . '%');
                }
            })->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }


}