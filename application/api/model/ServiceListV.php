<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 1:27 AM
 */

namespace app\api\model;


class ServiceListV extends BaseModel
{
    public function getCoverAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    public static function getList($area, $page, $size, $c_id, $type)
    {
        $pagingData = self::where('area', '=', $area)
            ->where('type', '=', $type)
            ->where(function ($query) use ($c_id) {
                if ($c_id) {
                    $query->where('c_id', '=', $c_id);
                }
            })
            ->hidden(['type','province','city','c_id','shop_name'])
            ->order('sell_num desc,sell_money desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;

    }

}