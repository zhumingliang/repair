<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 11:19 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;

class IndexServiceV extends BaseModel
{

    public function getCoverAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    /**
     * 首页显示服务（家政/维修）
     * @param $area
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList($area)
    {
        $list = self::where('area', '=', $area)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->hidden(['province', 'city', 'c_id', 'shop_name','id','area','state'])
            ->select();

        return $list;

    }
}