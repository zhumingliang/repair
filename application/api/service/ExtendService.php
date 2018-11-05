<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 4:17 PM
 */

namespace app\api\service;


use app\api\model\CollectionServicesT;
use app\api\model\ExtendRecordT;
use app\api\model\ExtendV;
use app\api\model\IndexServiceV;
use app\api\model\ServiceExtendT;
use app\api\model\ServicesExtendV;
use app\api\model\ServicesT;
use app\api\model\ServiceV;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\ExtendException;
use think\Db;
use think\Exception;

class ExtendService
{
    /**
     * CMS(管理员/加盟商)获取推广列表
     * @param $type
     * @param $page
     * @param $size
     * @param $keyW
     * @return array|\think\Paginator
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     * @throws \think\exception\DbException
     */
    public static function getList($type, $page, $size, $keyW)
    {
        $list = array();
        // $u_id = Token::getCurrentUid();
        $grade = Token::getCurrentTokenVar('grade');
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            $list = ServicesExtendV::where('state', '=', $type)
                ->where(function ($query) use ($keyW) {
                    if ($keyW) {
                        $query->where('service_name', 'like', '%' . $keyW . '%');
                    }
                })
                ->paginate($size, false, ['page' => $page]);

        } else if ($grade == UserEnum::USER_GRADE_JOIN) {
            $list_where = self::getListWhereJoin();

            $list = ServicesExtendV::where('state', '=', $type)
                ->where($list_where['key'], '=', $list_where['value'])
                ->where(function ($query) use ($keyW) {
                    if ($keyW) {
                        $query->where('service_name', 'like', '%' . $keyW . '%');
                    }
                })
                ->paginate($size, false, ['page' => $page]);
        }
        return $list;

    }

    /**
     * 获取加盟商级别
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private static function getListWhereJoin()
    {
        $province = Token::getCurrentTokenVar('grade');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');
        $where_area = [];
        if ($area == '全部') {
            if ($city == '全部') {
                $where_area['key'] = 'province';
                $where_area['value'] = $province;

            } else {
                $where_area['key'] = 'city';
                $where_area['value'] = $city;
            }

        } else {
            $where_area['key'] = 'area';
            $where_area['value'] = $area;
        }

        return $where_area;
    }

    /**
     * 操作推广记录状态
     * @param $id
     * @param $type
     * @throws Exception
     */
    public static function handel($id, $type)
    {

        Db::startTrans();
        try {

            $res = ServiceExtendT::update(['state' => $type], ['id' => $id]);
            if (!$res) {
                throw  new  ExtendException();
            }
            $record = [
                'e_id' => $id,
                'u_id' => Token::getCurrentUid(),
                'state' => $type];
            $record_res = ExtendRecordT::create($record);
            if (!$record_res) {
                Db::rollback();
                throw new ExtendException(
                    ['code' => 401,
                        'msg' => '添加操作记录失败',
                        'errorCode' => 130002
                    ]
                );
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }


    /**
     * @param $extend_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getTheService($extend_id)
    {
        return ServiceV::where('extend_id', '=', $extend_id)
            ->find();


    }


    /**
     * @param $area
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getIndexServiceList($area)
    {
        //return ExtendV::getList($area, $size, $page, $c_id, CommonEnum::EXTEND_HOUSE);
        return IndexServiceV::getList($area);

    }


    /**
     * @param $area
     * @param $size
     * @param $page
     * @param $c_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getHoursList($area, $size, $page, $c_id)
    {
        //return ExtendV::getList($area, $size, $page, $c_id, CommonEnum::EXTEND_HOUSE);
        return IndexServiceV::getList($area);

    }

    /**
     * 首页维修服务推广
     * @param $area
     * @param $size
     * @param $page
     * @param $c_id
     * @return \think\Paginator
     */
    public static function getRepairList($area, $page, $size, $c_id)
    {
        return ExtendV::getList($area, $size, $page, $c_id, CommonEnum::EXTEND_REPAIR);

    }

    /**
     * 小程序获取指定服务信息
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getServiceForMini($id)
    {
        $service = ServicesT::getService($id);
        $service['collection'] = self::checkCollection($id);
        return $service;


    }

    /**
     * 检查用户是否收藏该服务
     * @param $id
     * @return int|mixed
     * @throws Exception
     * @throws \app\lib\exception\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function checkCollection($id)
    {
        $col = CollectionServicesT::
        where('u_id', '=', Token::getCurrentUid())
            ->where('s_id', '=', $id)
            //->where('state', CommonEnum::STATE_IS_OK)
            ->find();

        if (!$col) {
            return 0;
        }
        if ($col->state == CommonEnum::STATE_IS_OK) {
            return $col->id;
        } else {
            return 0;
        }


    }

}