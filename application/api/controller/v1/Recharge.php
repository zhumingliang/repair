<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/13
 * Time: 5:31 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\RechargeT;
use app\api\service\RechargeS;
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
        (new RechargeS())->save($count, $score);
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
        $recharge = new RechargeS();
        return json([
            'score' => $score = $recharge->exchange($code)
        ]);

    }

    /**
     * @api {GET} /api/v1/recharges 362-获取兑换积分码列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取兑换积分码列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/recharges?size=20&page=1
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":20,"per_page":"2","current_page":1,"last_page":10,"data":[{"id":12,"code":"PFkGJGz0mn","state":1,"score":20,"create_time":"2019-02-25 06:31:26"},{"id":20,"code":"t3exHKwtkF","state":1,"score":20,"create_time":"2019-02-25 06:31:26"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 积分码id
     * @apiSuccess (返回参数说明) {int} score 积分
     * @apiSuccess (返回参数说明) {int} state 状态：1 | 未使用；2| 已兑换
     * @apiSuccess (返回参数说明) {string} code 兑换码
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($page = 1, $size = 20)
    {
        $list = RechargeT::getList($page, $size);
        return json($list);


    }

}