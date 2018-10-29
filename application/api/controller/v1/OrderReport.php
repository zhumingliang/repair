<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/28
 * Time: 10:49 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\OrderReportV;
use app\api\service\OrderReportService;

class OrderReport extends BaseController
{
    /**
     * 管理员获取需求订单列表
     * @param $key
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getDemandReportForAdmin($key, $order_type, $page, $size)
    {
        $list = (new OrderReportService())->demandReportForAdmin($key, $order_type, $page, $size);
        return json($list);

    }

    /**
     * 管理员获取服务订单列表
     * @param $key
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getServiceReportForAdmin($key, $order_type, $page, $size)
    {

        $list = (new OrderReportService())->serviceReportForAdmin($key, $order_type, $page, $size);
        return json($list);
    }

    /**
     * 加盟商获取订单列表
     * @param $type
     * @param $key
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getOrderReportForJoin($type, $key, $page, $size)
    {
        $list = array();
        if ($type == 1) {
            $list = OrderReportV::noCompleteForJoin($key, $page, $size);
        } else if ($type == 2) {
            $list = OrderReportV::completeForJoin($key, $page, $size);

        }

        return json($list);

    }



}