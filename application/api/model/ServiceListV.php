<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 1:27 AM
 */

namespace app\api\model;


use app\api\service\ExtendService;

class ServiceListV extends BaseModel
{
//     public function getCoverAttr($value, $data)
//     {
//         return $this->prefixImgUrl($value, $data);
//     }


    public static function getList($area, $page, $size, $c_id, $type)
    {
        $pagingData = self::where('area', '=', $area)
            ->where('type', '=', $type)
            ->where(function ($query) use ($c_id) {
                if ($c_id) {
                    $query->where('c_id', '=', $c_id);
                }
            })
            ->hidden(['type', 'province', 'city', 'c_id', 'shop_name'])
            ->order('sell_num desc,sell_money desc')
            ->paginate($size, false, ['page' => $page])->toArray();

        $data = $pagingData['data'];
        if (count($data)) {
            foreach ($data as $k => $v) {
                $data[$k]['extend'] = ExtendService::checkExtend($v['id']);
            }
            $pagingData['data'] = $data;
        }
        return $pagingData;

    }

    public static function getListForSell($type, $area, $key, $page, $size)
    {

        $order = 'id';
        if ($type == 5) {
            $order = 'sell_money desc';
        } else if ($type == 6) {
            $order = 'sell_money';
        }
        $pagingData = self::where('area', $area)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('name', 'like', '%' . $key . '%');
                }
            })
            ->field('id,cover,name,sell_money as price')
            ->order($order)
            ->paginate($size, false, ['page' => $page]);
        return $pagingData;
    }


}