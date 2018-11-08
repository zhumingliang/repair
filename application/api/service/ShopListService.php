<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/23
 * Time: 3:14 PM
 */

namespace app\api\service;


use app\api\model\ShopT;
use app\lib\enum\UserEnum;
class ShopListService
{

    private $ready = 1;
    /**
     * @param $page
     * @param $size
     * @param $type
     * @param $key
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getShops($page, $size, $type, $key)
    {
        $grade = Token::getCurrentTokenVar('grade');
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            //获取所有
            if ($type == $this->ready) {
                return self::getReady($page, $size, $key);
            }
            return self::shopsForAdmin($page, $size, $key);
        } else {
            $province = Token::getCurrentTokenVar('province');
            $city = Token::getCurrentTokenVar('city');
            $area = Token::getCurrentTokenVar('area');
            //获取加盟商所有店铺
            return self::shopsForJoin($province, $city, $area, $page, $size, $key,$type);
        }
    }

    /**
     * 管理员获取待审核列表
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    private function getReady($page, $size, $key)
    {
        return ShopT::readyList($page, $size, $key);
    }

    /**
     * 管理员获取审核通过列表
     * @param $page
     * @param $size
     * @param $key
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    private function shopsForAdmin($page, $size, $key)
    {
        return ShopT::passList($page, $size, $key);

    }

    /**
     * @param $province
     * @param $city
     * @param $area
     * @param $page
     * @param $size
     * @param $key
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    private function shopsForJoin($province, $city, $area, $page, $size, $key,$type)
    {

        return ShopT::shopsForJoin($province, $city, $area, $page, $size, $key,$type);
    }

}