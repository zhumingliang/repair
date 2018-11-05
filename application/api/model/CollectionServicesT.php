<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:33
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use think\Model;

class CollectionServicesT extends Model
{

    public function getCoverAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    public function service()
    {
        return $this->belongsTo('ServicesT',
            's_id', 'id');
    }

    /**
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function getList($page, $size)
    {
        $pagingData = self::with(['service' => function ($query) {
            $query->field('id,cover,name,price/100 as price');
        }])->where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('u_id', '=',12)
            ->field('id,s_id')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();


       /* if (isset($pagingData['data'])) {
            $data = $pagingData['data'];
            if (count($data)) {
                foreach ($data as $k => $v) {
                    $data[$k]['service']['price'] =  $data[$k]['service']['price'] / 100;
                }
            }
            $pagingData['data'] = $data;

        }*/

        return $pagingData;
    }


}