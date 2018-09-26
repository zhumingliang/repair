<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:36
 */

namespace app\api\service;

use app\api\model\CollectionServiceT;
use app\api\model\CollectionShopT;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\CollectionException;

class CollectionService
{
    /**
     * 新增收藏
     * @param $id
     * @param $type
     * @throws CollectionException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function save($id, $type)
    {
        $u_id = TokenService::getCurrentUid();
        $data = [
            's_id' => $id,
            'u_id' => $u_id,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $save_id = 0;
        if ($type === 1) {
            //收藏服务
            $save_id = CollectionServiceT::create($data);
        } else if ($type == 2) {
            //收藏店铺
            $save_id = CollectionShopT::create($data);
        }
        if (!$save_id) {
            throw  new  CollectionException();

        }

    }

    /**
     * 取消收藏
     * @param $id
     * @param $type
     * @throws CollectionException
     */
    public static function handel($id, $type)
    {
        $save_id = 0;
        if ($type === 1) {
            //收藏服务
            $save_id = CollectionServiceT::update(['state' => CommonEnum::STATE_IS_FAIL],
                ['id' => $id]);
        } else if ($type == 2) {
            //收藏店铺
            $save_id = CollectionShopT::update(['state' => CommonEnum::STATE_IS_FAIL],
                ['id' => $id]);
        }
        if (!$save_id) {
            throw  new  CollectionException(
                ['code' => 401,
                'msg' => '收藏操作失败',
                'errorCode' => 80002
            ]);

        }
    }

}