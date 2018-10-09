<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:29 PM
 */

namespace app\api\service;


use app\api\model\CircleCategoryT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\ParameterException;

class CircleService
{

    /**
     * CMS 获取圈子类别列表（管理员-圈子分类列表/加盟商-新增圈子时获取分类列表）
     * @param $params
     * @return array|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategoryListForCms($params)
    {
        $grade = Token::getCurrentTokenVar('grade');
        $list = array();
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            $page = $params['page'];
            $size = $params['size'];
            $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
                ->hidden(['state', 'update_time'])
                ->paginate($size, false, ['page' => $page]);

        } else if ($grade == UserEnum::USER_GRADE_JOIN) {
            $province = Token::getCurrentTokenVar('province');
            $city = Token::getCurrentTokenVar('city');
            $area = Token::getCurrentTokenVar('area');
            $sql = preJoinSql($province, $city, $area);
            $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
                ->whereRaw($sql)
                ->hidden(['state', 'create_time', 'update_time', 'province', 'city', 'area'])
                ->select();
        }


        return $list;
    }

    /**
     * 小程序获取指定区域内圈子列表
     * @param $params
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategoryListForMini($params)
    {
        $province = $params['province'];
        $city = $params['city'];
        $area = $params['area'];
        $sql = preJoinSql($province, $city, $area);
        $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->hidden(['state', 'create_time', 'update_time', 'province', 'city', 'area'])
            ->select();
        return $list;

    }

    /**
     * 获取加盟商代理级别
     * @return array
     * @throws ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private static function getWhereOp()
    {
        $sql = '	province = "全部"
OR ( province = "安徽省" AND city="全部")
OR (province = "安徽省" AND city="铜陵市" AND area="全部")
OR (province = "安徽省" AND city="铜陵市" AND area="铜官区")';
        /*$whereOp = array();
        $province = Token::getCurrentTokenVar('province');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');
        if ($area != "全部") {
            $whereOp['field'] = 'area';
            $whereOp['value'] = $area;
            return $whereOp;
        }

        if ($city != "全部") {
            $whereOp['field'] = 'city';
            $whereOp['value'] = $city;
            return $whereOp;
        }

        if ($province != "全部") {
            $whereOp['field'] = 'province';
            $whereOp['value'] = $province;
            return $whereOp;
        }

        throw new ParameterException();*/

    }

}