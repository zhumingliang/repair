<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:36
 */

namespace app\api\service;

use app\api\model\CollectionServicesT;
use app\api\model\CollectionShopT;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\CollectionException;

class CollectionService
{
    const COLLECTION_HOUSE = 1;
    const COLLECTION_REPAIR = 2;


    /**
     *  新增收藏
     * @param $id
     * @param $type
     * @return mixed
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
        if ($type === self::COLLECTION_HOUSE) {
            //收藏服务
            $save = CollectionServicesT::create($data);
            if (!$save->id) {
                throw  new  CollectionException();

            }
            return $save->id;
        } else if ($type == self::COLLECTION_REPAIR) {
            //收藏店铺
            $save = CollectionShopT::create($data);
            if (!$save->id) {
                throw  new  CollectionException();

            }
            return $save->id;
        }


    }

    /**
     * 取消收藏
     * @param $id
     * @param $type
     * @throws CollectionException
     */
    public
    static function handel($id, $type)
    {
        $save_id = 0;
        if ($type === self::COLLECTION_HOUSE) {
            //收藏服务
            $save_id = CollectionServicesT::update(['state' => CommonEnum::STATE_IS_FAIL],
                ['s_id' => $id, 'u_id' => Token::getCurrentUid()]);
        } else if ($type == self::COLLECTION_REPAIR) {
            //收藏店铺
            $save_id = CollectionShopT::update(['state' => CommonEnum::STATE_IS_FAIL],
                ['s_id' => $id, 'u_id' => Token::getCurrentUid()]);
        }
        if (!$save_id) {
            throw  new  CollectionException(
                ['code' => 401,
                    'msg' => '收藏操作失败',
                    'errorCode' => 80002
                ]);

        }
    }

    /**
     * 获取收藏列表
     * @param $type
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     */
    public
    static function getList($type, $page, $size)
    {
        $obj = [];
        if ($type == self::COLLECTION_REPAIR) {
            $obj = CollectionShopT::getList($page, $size);
        } else if ($type == self::COLLECTION_HOUSE) {
            $obj = CollectionServicesT::getList($page, $size);

        }

        return $obj;

    }

}