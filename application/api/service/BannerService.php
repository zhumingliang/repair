<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午1:30
 */

namespace app\api\service;


use app\api\model\BannerT;
use app\lib\enum\CommonEnum;
use app\lib\exception\BannerException;

class BannerService
{

    const PLATFORM = 1;
    const JOIN = 2;

    /**
     * 新增轮播图
     * @param $params
     * @throws BannerException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function save($params)
    {
        $u_id = Token::getCurrentUid();
        $grade = Token::getCurrentTokenVar('grade');
        $state = $grade == 1 ? CommonEnum::PASS : CommonEnum::READY;
        $params['u_id'] = $u_id;
        $params['type'] = $grade;
        $params['state'] = $state;
        if (isset($params['img'])) {
            $params['url'] = base64toImg($params['img']);
        }
        $id = BannerT::create($params);
        if (!$id) {
            throw  new  BannerException();

        }

    }

    /**
     * 修改轮播图
     * @param $params
     * @throws BannerException
     */
    public static function update($params)
    {

        if (isset($params['img'])) {
            $params['url'] = base64toImg($params['img']);
        }

        $id = BannerT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new BannerException(['code' => 401,
                'msg' => '修改轮播图失败',
                'errorCode' => 100004
            ]);

        }

    }

    public static function getListForMini($params)
    {
        $type = $params['type'];
        if ($type == self::PLATFORM) {
            $list = BannerT::where('type', '=', $type)
                ->where('state', '=', CommonEnum::STATE_IS_OK)
                ->order('create_time desc')
                ->select();

            return $list;
        } else if ($type == self::JOIN) {

        }

    }

}