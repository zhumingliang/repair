<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/6
 * Time: 11:36 PM
 */

namespace app\api\model;


class ExtendV extends BaseModel
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
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;

    }

}