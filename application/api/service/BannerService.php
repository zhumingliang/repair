<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午1:30
 */

namespace app\api\service;


use app\api\model\BannerMiniV;
use app\api\model\BannerT;
use app\api\model\ImgT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
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
            $params['url'] = ImageService::getImageUrl($params['img']);
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
            $params['url'] = ImageService::getImageUrl($params['img']);
        }

        $id = BannerT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new BannerException(['code' => 401,
                'msg' => '修改轮播图失败',
                'errorCode' => 100004
            ]);

        }

    }

    /**
     * 获取首页轮播图
     * @param $params
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getListForMini($params)
    {
        $type = $params['type'];
        $list = array();
        if ($type == self::PLATFORM) {
            $list = BannerT::where('type', '=', $type)
                ->where('state', '=', CommonEnum::PASS)
                ->field('id,title,des,url')
                ->order('create_time desc')
                ->select();
        } else if ($type == self::JOIN) {
            $province = $params['province'];
            $city = $params['city'];
            $area = $params['area'];
            $sql = preJoinSql($province, $city, $area);
            $category = $params['category'] == 3 ? 4 : 3;
            $list = BannerMiniV::where('state', '=', CommonEnum::PASS)
                ->where('category', $category)
                ->whereRaw($sql)
                ->field('id,title,des,url')
                ->select();
        }
        return $list;

    }


    /**
     * @param $params
     * @return array|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function getListForCMS($params)
    {
        $type = $params['type'];
        $page = $params['page'];
        $size = $params['size'];

        $list = array();
        if ($type == self::PLATFORM) {
            $list = BannerT::where('type', '=', $type)
                ->where('state', '<', CommonEnum::DELETE)
                ->field('id,title,des,url')
                ->order('create_time desc')
                ->paginate($size, false, ['page' => $page]);
        } else if ($type == self::JOIN) {
            $list = self::getListForCMSJoin($page, $size);

        }
        return $list;

    }


    /**
     * CMS获取加盟商banner图片
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private static function getListForCMSJoin($page, $size)
    {
        $list = array();
        $grade = Token::getCurrentTokenVar('grade');
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            $list = BannerMiniV::where('state', CommonEnum::READY)
                ->paginate($size, false, ['page' => $page]);

        } else if ($grade == UserEnum::USER_GRADE_JOIN) {
            $list = BannerMiniV::where('state', '<', CommonEnum::DELETE)
                ->where('u_id', '=', Token::getCurrentUid())
                ->hidden(['u_id'])
                ->paginate($size, false, ['page' => $page]);

        }

        return $list;

    }


}