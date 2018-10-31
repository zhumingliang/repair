<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/31
 * Time: 10:12 AM
 */

namespace app\api\model;


class StaffV extends BaseModel
{

    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

    public static function getList($page, $size)
    {
        $pagingData = self::order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;

    }

}