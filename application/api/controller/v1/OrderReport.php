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
use app\api\service\OrderService;

class OrderReport extends BaseController
{

    /**
     * @api {GET} /api/v1/report/demand/admin 138-管理员-订单管理-需求订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/demand/admin?page=1&size=10&order_type=3
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ： order_type=1 全部状态；
     * order_type=2 未完成；order_type=3 已完成
     * 商铺订单类别：1 | 待服务；2 | 待确认；3 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":5,"per_page":"1","current_page":1,"last_page":5,"data":[{"order_id":33,"source_id":26,"shop_id":5,"source_name":"需要做饭和打扫卫生的阿姨","update_money":1,"phone_shop":2,"phone_user":2,"user_name":"朱明良","prepay_id":"","pay_money":0,"shop_name":"维修小家","time_begin":"2018-10-28 16:46:00","time_end":"2018-11-04 16:46:00","order_number":"BA26611961932221","shop_confirm":2,"order_time":"2018-10-26 21:39:56","address":"铜陵市第十一中学(第十一中学铜井路北)","origin_money":1,"user_phone":"18956225238","shop_phone":"18956225230","comment_id":99999,"confirm_id":99999,"pay_id":99999,"refund_id":99999,"state":1,"u_id":15,"service_begin":2,"cover":"https:\/\/mengant.cn\/static\/imgs\/20181024\/339bbbfc439017646ab6fa4da01634b8.jpg","province":"安徽省","city":"铜陵市","area":"郊区","read_money":null,"order_state":"未完成"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} order_number 订单号
     * @apiSuccess (返回参数说明) {String} user_name 下单人
     * @apiSuccess (返回参数说明) {String} user_phone 电话
     * @apiSuccess (返回参数说明) {String} shop_name 商家
     * @apiSuccess (返回参数说明) {String} source_name 服务名称
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {int} update_money 费用
     * @apiSuccess (返回参数说明) {int} read_money 红包金额
     * @apiSuccess (返回参数说明) {String} order_time 下单时间
     * @apiSuccess (返回参数说明) {String}  order_state 状态
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
     * @api {GET} /api/v1/report/service/admin 139-管理员-订单管理-服务订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/service/admin?page=1&size=10&order_type=3
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ： order_type=1 未完成；
     * order_type=2 待评价；order_type=3 已完成；order_type=4 全部状态
     * 商铺订单类别：1 | 待服务；2 | 待确认；3 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":5,"per_page":"1","current_page":1,"last_page":5,"data":[{"order_id":33,"source_id":26,"shop_id":5,"source_name":"需要做饭和打扫卫生的阿姨","update_money":1,"phone_shop":2,"phone_user":2,"user_name":"朱明良","prepay_id":"","pay_money":0,"shop_name":"维修小家","time_begin":"2018-10-28 16:46:00","time_end":"2018-11-04 16:46:00","order_number":"BA26611961932221","shop_confirm":2,"order_time":"2018-10-26 21:39:56","address":"铜陵市第十一中学(第十一中学铜井路北)","origin_money":1,"user_phone":"18956225238","shop_phone":"18956225230","comment_id":99999,"confirm_id":99999,"pay_id":99999,"refund_id":99999,"state":1,"u_id":15,"service_begin":2,"cover":"https:\/\/mengant.cn\/static\/imgs\/20181024\/339bbbfc439017646ab6fa4da01634b8.jpg","province":"安徽省","city":"铜陵市","area":"郊区","read_money":null,"order_state":"未完成"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} order_number 订单号
     * @apiSuccess (返回参数说明) {String} user_name 下单人
     * @apiSuccess (返回参数说明) {String} user_phone 电话
     * @apiSuccess (返回参数说明) {String} shop_name 商家
     * @apiSuccess (返回参数说明) {String} source_name 服务名称
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {int} update_money 费用
     * @apiSuccess (返回参数说明) {int} read_money 红包金额
     * @apiSuccess (返回参数说明) {String} order_time 下单时间
     * @apiSuccess (返回参数说明) {String}  order_state 状态
     *
     * @param $key
     * @param $order_type
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getServiceReportForAdmin($order_type, $page, $size, $keyw = '')
    {
        $list = (new OrderReportService())->serviceReportForAdmin($keyw, $order_type, $page, $size);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/report/order/join 140-加盟商-订单列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/order/join?page=1&size=10&order_type=2
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ： order_type=1 未完成；order_type=2 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":5,"per_page":"1","current_page":1,"last_page":5,"data":[{"order_id":33,"source_id":26,"shop_id":5,"source_name":"需要做饭和打扫卫生的阿姨","update_money":1,"phone_shop":2,"phone_user":2,"user_name":"朱明良","prepay_id":"","pay_money":0,"shop_name":"维修小家","time_begin":"2018-10-28 16:46:00","time_end":"2018-11-04 16:46:00","order_number":"BA26611961932221","shop_confirm":2,"order_time":"2018-10-26 21:39:56","address":"铜陵市第十一中学(第十一中学铜井路北)","origin_money":1,"user_phone":"18956225238","shop_phone":"18956225230","comment_id":99999,"confirm_id":99999,"pay_id":99999,"refund_id":99999,"state":1,"u_id":15,"service_begin":2,"cover":"https:\/\/mengant.cn\/static\/imgs\/20181024\/339bbbfc439017646ab6fa4da01634b8.jpg","province":"安徽省","city":"铜陵市","area":"郊区","read_money":null,"order_state":"未完成","shop_type":1}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {int} shop_id  商户id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} shop_type  服务类别：1| 维修服务；2 | 家政服务
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} update_money 价格
     * @apiSuccess (返回参数说明) {String} order_time 下单时间
     ** @param $order_type
     * @param $key
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getOrderReportForJoin($order_type, $page, $size, $key = '')
    {
        $list = array();
        if ($order_type == 1) {
            $list = OrderReportV::noCompleteForJoin($key, $page, $size);
        } else
            if ($order_type == 2) {
                $list = OrderReportV::completeForJoin($key, $page, $size);
            }

        return json($list);

    }

    /**
     * @api {GET} /api/v1/report/export/city 141-管理员-订单管理-城市订单导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 直接访问连接下载数据
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/export/city?province=安徽省&city=铜陵市&time_begin=2018-08-01&time_end=2018-10-30&token=sdadas
     * @apiParam (请求参数说明) {String} province  省
     * @apiParam (请求参数说明) {String} city  市
     * @apiParam (请求参数说明) {String} time_begin 开始时间
     * @apiParam (请求参数说明) {String} time_end 结束时间
     * @apiParam (请求参数说明) {String} token 授权token
     *
     * @param $province
     * @param $city
     * @param $time_begin
     * @param $token
     * @param $time_end
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public
    function exportWithCity($province, $city, $time_begin, $time_end, $token)
    {
        (new OrderReportService())->exportReportForCity($province, $city, $time_begin, $time_end, $token);

    }

    /**
     * @api {GET} /api/v1/report/export 142-管理员-订单管理-平台订单管理/加盟商-导出订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 直接访问连接下载数据
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/report/export?time_begin=2018-08-01&time_end=2018-10-30&token=sdadas
     * @apiParam (请求参数说明) {String} time_begin 开始时间
     * @apiParam (请求参数说明) {String} time_end 结束时间
     * @apiParam (请求参数说明) {String} token 授权token
     *
     * @param $token
     * @param $time_begin
     * @param $time_end
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public
    function exportWithoutCity($time_begin, $time_end, $token)
    {
        (new OrderReportService())->exportReport($time_begin, $time_end, $token);

    }

    /**
     * @api {GET} /api/v1/order/banner 188-小程序获取服务轮播数据
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/order/banner?province=安徽省&city=铜陵市&area&type=3
     * @apiParam (请求参数说明) {String} province  省
     * @apiParam (请求参数说明) {String} city  市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {int} type  数据类别 ： 1 | 维修服务轮播；type=2 家政服务轮播；type=3 全部轮播
     * 商铺订单类别：1 | 待服务；2 | 待确认；3 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * [{"user_name":"测试2222","source_name":"测试222"},{"user_name":"家政需求","source_name":"测试"},{"user_name":"颖儿","source_name":"航空"},{"user_name":"测试保姆","source_name":"测试保姆"},{"user_name":"颖儿","source_name":"航空"},{"user_name":"颖儿","source_name":"航空"},{"user_name":"@敬超","source_name":"打酱油"},{"user_name":"@敬超","source_name":"打酱油"},{"user_name":"@敬超","source_name":"打酱油"}]
     * @apiSuccess (返回参数说明) {String} user_name 用户名
     * @apiSuccess (返回参数说明) {String} source_name 服务名称
     *
     * @param $province
     * @param $city
     * @param $area
     * @param $type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrdersToBanner($province, $city, $area, $type)
    {
        $list = (new OrderService())->ordersToBanner($province, $city, $area, $type);
        return json($list);
    }


}