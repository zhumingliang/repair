<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:43 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\BondT;
use app\api\model\WithdrawMiniT;
use app\api\service\WithDrawService;
use app\api\validate\WithdrawValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\api\service\Token as TokenService;

class Withdraw extends BaseController
{


    /**
     * @api {POST} /api/v1/withdraw/apply  93-用户/店铺提交提现申请
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1,
     *       "money": 500
     *     }
     * @apiParam (请求参数说明) {int} type   提现类别：1 | 保证金；2| 服务费用
     * @apiParam (请求参数说明) {int} money    金额
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \app\lib\exception\WithdrawException
     * @throws \think\Exception
     */
    public function apply()
    {
        (new WithdrawValidate())->scene('apply')->goCheck();
        $params = $this->request->param();
        WithDrawService::apply($params['type'], $params['money']);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/withdraw/check  92-检查用户是否有未处理的提现订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/check?type=1
     * @apiParam (请求参数说明) {int} type   提现类别：1 | 保证金；2| 服务费用
     * @apiSuccessExample {json} 返回样例:
     * {"state":1}
     * @apiSuccess (返回参数说明) {int} state 是否有未处理的提现订单：1 | 无；2 |有
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function checkWithdraw()
    {
        (new WithdrawValidate())->scene('check')->goCheck();
        $type = $this->request->param('type');
        $count = WithdrawMiniT::where('type', $type)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', CommonEnum::ORDER_STATE_INIT)
            ->where('u_id', TokenService::getCurrentUid())
            ->count();
  
        return json(['state' => $count + 1]);
    }

    /**
     * @api {GET} /api/v1/withdraw/balance  90-获取用户/商家可提现余额
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/balance
     * @apiSuccessExample {json} 店铺返回样例:
     * {"bond_balance":500,"business_balance":10000}
     * @apiSuccess (返回参数说明) {int} bond_balance 保证金余额
     * @apiSuccess (返回参数说明) {int} business_balance 营业额余额
     * * @apiSuccessExample {json} 用户返回样例:
     * {"balance":0}
     * @apiSuccess (返回参数说明) {int} balance 用户余额
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getBalance()
    {
        $res = WithDrawService::getBalance();
        return json($res);


    }

    /**
     * @api {GET} /api/v1/withdraw/bond/check  91-检查用户保证金本月申请提现次数
     * @apiGroup  MINI 不能超过3次
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/bond/check
     * @apiSuccessExample {json} 店铺返回样例:
     * {"state":1}
     * @apiSuccess (返回参数说明) {int} state 是否超过三次：1 | 否；2 | 是（超过三次不能发起提现申请）
     *
     * @return array
     */
    public function checkBond()
    {
        $count = BondT::where('u_id', 1)->whereTime('create_time', 'month')
            ->count();
        if ($count < 3) {
            return json(['state' => 1]);
        } else {
            return json(['state' => 2]);
        }
    }

}