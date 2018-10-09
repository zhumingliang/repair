<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:29 PM
 */

namespace app\api\service;


use app\api\model\CircleCategoryT;
use app\api\model\CircleExamineT;
use app\api\model\CircleT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\CircleException;

class CircleService
{
    const CIRCLE_NEED_EXAMINE = 2;
    const CIRCLE_NOT_TOP = 1;
    const CIRCLE_TOP = 2;

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
     * 保存圈子
     * @param $params
     * @throws CircleException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function saveCircle($params)
    {
        $u_id = Token::getCurrentUid();
        $grade = Token::getCurrentTokenVar('grade');
        $params['u_id'] = $u_id;
        $params['state'] = self::checkCircleDefault() == self::CIRCLE_NEED_EXAMINE ? CommonEnum::READY : CommonEnum::PASS;
        $params['top'] = self::CIRCLE_NOT_TOP;
        $params['read_num'] = 0;
        $params['province'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('province');
        $params['city'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('city');
        $params['area'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('area');
        $params['parent_id'] = Token::getCurrentTokenVar('grade') == UserEnum::USER_GRADE_JOIN ? Token::getCurrentUid() : Token::getCurrentTokenVar('parent_id');
        if (isset($params['head_img'])) {
            $params['head_img'] = base64toImg($params['head_img']);
        }

        $id = CircleT::create($params);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '新增圈子失败',
                'errorCode' => 160004
            ]);

        }
    }

    /**
     * 查看圈子是否审核默认设置
     * @return array|int|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function checkCircleDefault()
    {
        $default = (new CircleExamineT())->field('default')->find();

        return $default ? $default->default : 2;
    }


    public static function getCircleListForCMS($params)
    {
        $list = CircleT::getListForCms($params['page'], $params['size'], $params['state']);
        return $list;

    }


    public static function getCircleListForMINI($params)
    {
        $list = CircleT::getListForMINI($params['page'], $params['size'], $params['province'], $params['city'], $params['area'], $params['c_id']);
        return $list;

    }

    /**
     * 指定圈子阅读量加一
     * @param $id
     */
    private function incReadNum($id)
    {
        CircleT::where('id', $id)
            ->inc('read_num');

    }

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