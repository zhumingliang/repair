<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/12
 * Time: 11:39 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ScoreRechargeT;
use app\api\service\ScoreService;
use app\api\validate\ScoreValidate;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Score extends BaseController
{
    /**
     * @api {POST} /api/v1/score/recharge  171-给指定用户充值积分
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 给指定用户充值积分
     * @apiExample {post}  请求样例:
     *    {
     *       "u_id": 100
     *       "score": 1000
     *       "remark": "缴费充值"
     *     }
     * @apiParam (请求参数说明) {int} u_id   用户id
     * @apiParam (请求参数说明) {int} score   充值积分
     * @apiParam (请求参数说明) {String} remark   备注
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $u_id
     * @param $score
     * @param string $remark
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function recharge($u_id, $score, $remark = '')
    {
        $res = ScoreRechargeT::create([
            'u_id' => $u_id,
            'score' => $score,
            'remark' => $remark,
            'admin_id' => \app\api\service\Token::getCurrentUid()
        ]);
        if (!$res) {
            throw new OperationException();
        }
        return json(new SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/score/buy  176-新增购买积分订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增购买积分订单
     * @apiExample {post}  请求样例:
     *    {
     *       "score": 1000
     *       "money": 100
     *     }
     * @apiParam (请求参数说明) {int} score   充值积分
     * @apiParam (请求参数说明) {int} money   购买金额:单位：分
     * @apiSuccessExample {json} 返回样例:
     * {"o_id":1}
     * @apiSuccess (返回参数说明) {int} o_id 订单id
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function buy()
    {
        (new ScoreValidate())->scene('buy')->goCheck();
        $params = $this->request->param();
        $id = (new ScoreService())->buy($params);
        return json(
            [
                'o_id' => $id
            ]
        );
    }


}