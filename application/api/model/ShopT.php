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
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->hidden(['u_id', 'create_time', 'update_time', 'frozen'])
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
            ->with([
                'staffs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '<', CommonEnum::DELETE);
                }
            ])
            ->field('id,name,province,city,area,phone,address,des')
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


    public static function getShopId($u_id)
    {
        $shop = self::where('u_id', $u_id)->find();
        return $shop['id'];

    }

}