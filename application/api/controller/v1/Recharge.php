<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/13
 * Time: 5:31 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\Service\RechargeService;
use app\lib\exception\SuccessMessage;

class Recharge extends BaseController
{

    /**
     * @api {POST} /api/v1/recharge/save  304-批量生成积分兑换码
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 批量生成积分兑换码
     * @apiExample {post}  请求样例:
     *    {
     *       "count": 100
     *       "score": 1000
     *     }
     * @apiParam (请求参数说明) {int} count   生成兑换码数量
     * @apiParam (请求参数说明) {int} score   生成兑换码的积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $count
     * @param $score
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     */
    public function save($count, $score)
    {
        (new RechargeService())->save($count, $score);
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/recharge/exchange  305-用户兑换积分码
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 批量生成积分兑换码
     * @apiExample {post}  请求样例:
     *    {
     *       "code": "0LGzP9"
     *     }
     * @apiParam (请求参数说明) {String} code  积分码
     * @apiSuccessExample {json} 返回样例:
     * {"score":1000}
     * @apiSuccess (返回参数说明) {int} score 兑换积分
     * @param $code
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function exchange($code)
    {
        $recharge = new RechargeService();
        return json([
            'score' => $score = $recharge->exchange($code)
        ]);

    }

}