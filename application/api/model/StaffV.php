<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/31
 * Time: 10:12 AM
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;

class StaffV extends BaseModel
{

    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    /**
     * @param $page
     * @param $size
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function getList($page, $size)
    {
        $province = Token::getCurrentTokenVar('province');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');

        $sql = preJoinSqlForGetDShops($province, $city, $area);

        $pagingData = self::where('state', CommonEnum::READY)
            ->whereRaw($sql)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }

}