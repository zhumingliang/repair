<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:28
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;

class ShopT extends BaseModel
{

    public function imgs()
    {
        return $this->hasMany('ShopImgT',
            's_id', 'id');
    }

    public function staffs()
    {
        return $this->hasMany('ShopStaffImgT',
            's_id', 'id');
    }

    /*  public function getHeadUrlAttr($value, $data)
      {
          return $this->prefixImgUrl($value, $data);
      }*/


    public static function getShopInfo($u_id)
    {
        $info = self::where('u_id', '=', $u_id)
            ->where('frozen', CommonEnum::STATE_IS_OK)
            ->whereIn('state',[1,2,4])
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->hidden(['u_id', 'create_time', 'update_time'])
            ->find();
        return $info;

    }

    public static function getShopInfoForCMS($id)
    {
        $info = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->hidden(['update_time', 'frozen'])
            ->find();
        return $info;

    }


    public static function getShopInfoForNormal($id)
    {
        $info = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->field('id,name,area,address,phone')
            ->find();
        return $info;

    }


    public static function getShopInfoForEdit($u_id)
    {
        $info = self::where('u_id', '=', $u_id)
            ->where('frozen', CommonEnum::STATE_IS_OK)
            ->where('state',4)
            ->with([
                'staffs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '<', CommonEnum::DELETE);
                }
            ])
            ->field('id,name,province,city,area,phone,address,des,head_url')
            ->find();
        return $info;

    }

    /**
     *获取指定区域内所有店铺
     * @param $province
     * @param $city
     * @param $area
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getShopsForFace($province, $city, $area)
    {
        $sql = preJoinSqlForGetDShops($province, $city, $area);
        $list = self::where('state', CommonEnum::PASS)
            ->whereRaw($sql)
            ->field('id')
            ->select()->toArray();

        return $list;

    }

    /**|
     * 获取店铺所属城市
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getShopCity($id)
    {
        $shop = self::where('id', $id)
            ->field('city')
            ->find();
        return $shop['city'];

    }

    /**
     * 管理员获取店铺待审核列表（全部）
     * @param $page
     * @param $size
     * @param $key
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function readyList($page, $size, $key)
    {
        $pagingData = self::where('state', CommonEnum::READY)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->field('id as shop_id,u_id,type,name,city,state')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData;

    }


    /**
     * 管理员获取店铺通过审核列表
     * @param $page
     * @param $size
     * @param $key
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function passList($page, $size, $key)
    {
        $pagingData = self::whereIn('state', [2, 4])
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->field('id as shop_id,u_id,type,name,city,state,frozen')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData;

    }

    /**
     *加盟商获取通过店铺列表
     * @param $province
     * @param $city
     * @param $area
     * @param $page
     * @param $size
     * @param $key
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function shopsForJoin($province, $city, $area, $page, $size, $key)
    {
        $sql = preJoinSqlForGetDShops($province, $city, $area);

        $pagingData = self::whereIn('state', '2,4')
            ->whereRaw($sql)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->field('id as shop_id,u_id,type,name,area,state,frozen')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData;


    }

}