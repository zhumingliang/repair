<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/28
 * Time: 10:49 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;

class OrderReport extends BaseController
{
    /**
     * 管理员获取需求订单列表
     * @param $key
     * @param $state
     */
    public function getDemandReportForAdmin($key, $state,$page,$size)
    {

    }

    /**
     * 管理员获取服务订单列表
     * @param $key
     * @param $state
     */
    public function getServiceReportForAdmin($key, $state,$page,$size)
    {


    }

    /**
     * 加盟商获取订单列表
     * @param $type
     * @param $name
     */
    public function getOrderReportForJoin($type, $name)
    {

    }

}