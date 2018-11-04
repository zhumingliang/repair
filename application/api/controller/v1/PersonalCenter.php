<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/20
 * Time: 7:38 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\OrderNormalMsgT;
use app\api\model\OrderShopMsgT;
use app\api\service\CenterService;
use app\api\service\OrderMsgService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OrderMsgException;
use app\lib\exception\SuccessMessage;

class PersonalCenter extends BaseController
{
    /**
     * @api {GET} /api/v1/center/info  102-获取用户-我的信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * http://mengant.cn/api/v1/center/info
     * @apiSuccessExample {json} 返回样例:
     * {"userInfo":{"avatarUrl":1,"nickName":1,"province":"安徽省","city":"铜陵市","area":"铜官区","phone":"18956225230"},"balance":0,"msg_count":1,"demand_count":1}
     * @apiSuccess (返回参数说明) {String} avatarUrl    用户头像
     * @apiSuccess (返回参数说明) {String} nickName    昵称
     * @apiSuccess (返回参数说明) {String} province    省
     * @apiSuccess (返回参数说明) {String} city    市
     * @apiSuccess (返回参数说明) {String} area    区
     * @apiSuccess (返回参数说明) {String} phone    手机号
     * @apiSuccess (返回参数说明) {int} balance  余额
     * @apiSuccess (返回参数说明) {int} msg_count  未读信息
     * @apiSuccess (返回参数说明) {int} demand_count  需求订单数量
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getInfo()
    {
        $info = (new CenterService)->getCenterInfo();
        return json($info);
    }

    /**
     * @api {GET} /api/v1/center/msgs 103-用户获取消息列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/center/msgs&page=1&size=5
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"5","current_page":1,"last_page":1,"data":[{"id":1,"order_id":1,"cover":"static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg","money":2000,"name":"修五金4","u_id":1,"state":1,"create_time":"2018-10-16 11:23:22"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 消息id
     * @apiSuccess (返回参数说明) {int} order_type 订单状态（和80/81 接口保持一致）
     * @apiSuccess (返回参数说明) {int} type 订单类别：1 | 需求订单；2 | 服务订单
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} cover 封面图
     * @apiSuccess (返回参数说明) {int} money  金额
     * @apiSuccess (返回参数说明) {String} name  名称
     * @apiSuccess (返回参数说明) {String} create_time  时间
     * @param $page
     * @param $size
     * @return \think\response\Json
     */
    public function getMsgs($page, $size)
    {
        $list = OrderMsgService::getList($page, $size);
        return json($list);
    }

    /**
     * @api {POST} /api/v1/center/msg/handel 104-消息状态修改
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户-我的-信息
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id  消息id
     * @apiParam (请求参数说明) {int} state  状态：2 | 阅读；3 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OrderMsgException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */

    public function handel($id)
    {
        $shop_id = \app\api\service\Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {
            $res = OrderNormalMsgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id', $id]);
        } else {
            $res = OrderShopMsgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id', $id]);
        }
        if (!$res) {
            throw new OrderMsgException(['code' => 401,
                'msg' => '信息阅读失败',
                'errorCode' => 21002
            ]);

        }
        return json(new  SuccessMessage());

    }

}