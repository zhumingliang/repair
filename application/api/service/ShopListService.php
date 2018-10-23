<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/23
 * Time: 3:14 PM
 */

namespace app\api\service;


use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;

class ShopListService
{

    private $ready = 1;
    private $all = 2;

    public function getShops($page, $size, $type)
    {
        $grade = Token::getCurrentTokenVar('grade');
        if ($grade == UserEnum::USER_GRADE_ADMIN) {


        } else {
            $province = Token::getCurrentTokenVar('province');
            $city = Token::getCurrentTokenVar('city');
            $area = Token::getCurrentTokenVar('area');

            //获取加盟商所有店铺

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
        return ShopT::readyList($page, $size);
    }

    private function shopsForJoin($province, $city, $area, $key)
    {

    }

}