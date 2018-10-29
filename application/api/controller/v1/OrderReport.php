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
     * @api {GET} /api/v1/report/demand/admin 138-管理员-订单管理-需求订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/demand/admin?page=1&size=10&order_type=3
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ： type=1 全部状态；type=2 未完成；type=3 已完成
     * 商铺订单类别：1 | 待服务；2 | 待确认；3 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":5,"per_page":"1","current_page":1,"last_page":5,"data":[{"order_id":33,"source_id":26,"shop_id":5,"source_name":"需要做饭和打扫卫生的阿姨","update_money":1,"phone_shop":2,"phone_user":2,"user_name":"朱明良","prepay_id":"","pay_money":0,"shop_name":"维修小家","time_begin":"2018-10-28 16:46:00","time_end":"2018-11-04 16:46:00","order_number":"BA26611961932221","shop_confirm":2,"order_time":"2018-10-26 21:39:56","area":"郊区","address":"铜陵市第十一中学(第十一中学铜井路北)","origin_money":1,"user_phone":"18956225238","shop_phone":"18956225230","comment_id":99999,"confirm_id":99999,"pay_id":99999,"refund_id":99999,"state":1,"u_id":15,"service_begin":2,"cover":"https:\/\/mengant.cn\/static\/imgs\/20181024\/339bbbfc439017646ab6fa4da01634b8.jpg"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} source_name 需求名称
     * @apiSuccess (返回参数说明) {String} time_begin 服务开始时间
     * @apiSuccess (返回参数说明) {String} time_end 服务结束时间
     * @apiSuccess (返回参数说明) {String} user_phone 用户手机号
     * @apiSuccess (返回参数说明) {String} shop_phone 店铺手机号
     * @apiSuccess (返回参数说明) {int} origin_money 订单原金额
     * @apiSuccess (返回参数说明) {int} update_money 订单修改之后金额
     * @apiSuccess (返回参数说明) {int} phone_user 商家是否联系用户：1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {int} phone_shop 用户是否联系商家：1 | 是；2 | 否
     *
     * @param $key
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getDemandReportForAdmin($order_type, $page = 1, $size = 20, $key = '')
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
    public function getServiceReportForAdmin($order_type, $page, $size, $key = '')
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
    public function getOrderReportForJoin($type, $page, $size, $key = '')
    {
        $list = array();
        if ($type == 1) {
            $list = OrderReportV::noCompleteForJoin($key, $page, $size);
        } else
            if ($type == 2) {
                $list = OrderReportV::completeForJoin($key, $page, $size);

            }

        return json($list);

    }

    /**
     * 管理员导出城市订单
     * @param $province
     * @param $city
     * @param $time_begin
     * @param $time_end
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public
    function exportWithCity($province, $city, $time_begin, $time_end)
    {
        (new OrderReportService())->exportReportForCity($province, $city, $time_begin, $time_end);

    }

    /**
     * 管理员导出平台订单/加盟商导出订单
     * @param $time_begin
     * @param $time_end
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public
    function exportWithoutCity($time_begin, $time_end)
    {
        (new OrderReportService())->exportReport($time_begin, $time_end);

    }


}