<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 4:04 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GoodsOrderT;
use app\api\service\GoodsOrderService;
use app\api\validate\GoodsOrderValidate;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class GoodsOrder extends BaseController
{
    /**
     * @api {POST} /api/v1/goods/order/save  328-新增积分兑换商品订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分兑换商品订单
     * @apiExample {post}  请求样例:
     *    {
     *       "g_id": 1
     *       "score": 1000
     *       "count": 1
     *       "a_id": 1
     *     }
     * @apiParam (请求参数说明) {int} g_id   商品id
     * @apiParam (请求参数说明) {int} score   总积分
     * @apiParam (请求参数说明) {int} count   数量
     * @apiParam (请求参数说明) {int} a_id   地址id
     * @apiSuccessExample {json} 返回样例:
     * {"res":1}
     * @apiSuccess (返回参数说明) {int} res 新增结果：1 | 新增成功；0 | 积分不够
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function saveOrder()
    {
        (new GoodsOrderValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        $res = (new GoodsOrderService())->save($params);
        return json([
            'res' => $res
        ]);

    }

    /**
     * @api {POST} /api/v1/goods/order/express/update  329-修改用户订单积分规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增用户订单积分规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "express": "顺丰快递"
     *       "express_code": "213123121"
     *     }
     * @apiParam (请求参数说明) {int} id   订单id
     * @apiParam (请求参数说明) {String} express   快递名称
     * @apiParam (请求参数说明) {String} express_code   订单号
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function updateExpress()
    {
        (new GoodsOrderValidate())->scene('express_update')->goCheck();
        $params = $this->request->param();
        $res = GoodsOrderT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 100002
            ]);

        }
        return json(new SuccessMessage());
    }

    public function getListForCMS($type, $page = 1, $size = 10)
    {
        (new GoodsOrderValidate())->scene('list')->goCheck();
        $list = (new GoodsOrderService())->getListForCMS($type, $page, $size);
        return json($list);


    }

    public function getListForMINI($type, $page = 1, $size = 10)
    {
        (new GoodsOrderValidate())->scene('list')->goCheck();
        $list = (new GoodsOrderService())->getListForMINI($type, $page, $size);
        return json($list);


    }

}