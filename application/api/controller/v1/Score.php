<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/12
 * Time: 11:39 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ScoreOrderRoleT;
use app\api\model\ScoreRechargeT;
use app\api\service\ScoreService;
use app\api\validate\ScoreValidate;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Score extends BaseController
{
    /**
     * @api {POST} /api/v1/score/recharge  306-给指定用户充值积分
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
     * @api {POST} /api/v1/score/buy  307-新增购买积分订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增购买积分订单
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {int} id   积分id
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
        (new ScoreValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        $id = (new ScoreService())->buy($params);
        return json(
            [
                'o_id' => $id
            ]
        );
    }

    /**
     * @api {POST} /api/v1/score/order/rule/save  308-新增用户注册/订单积分规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增用户注册/订单积分规则
     * @apiExample {post}  请求样例:
     *    {
     *       "self": 10000,
     *       "parent": 1000,
     *       "parent_other": 1000,
     *       "self_register": 1000,
     *       "parent_register": 1000
     *     }
     * @apiParam (请求参数说明) {int} self   用户自己获取积分比例
     * @apiParam (请求参数说明) {int} parent   用户上级获取积分比例
     * @apiParam (请求参数说明) {int} parent_other   用户上级获取附加积分比例
     * @apiParam (请求参数说明) {int} self_register   用户绑定邀请码自己获取的积分
     * @apiParam (请求参数说明) {int} parent_register   用户绑定邀请码邀请人获取的积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function scoreOrderRuleSave()
    {
        (new ScoreValidate())->scene('order_rule')->goCheck();
        $params = $this->request->param();
        $res = ScoreOrderRoleT::create($params);
        if (!$res) {
            throw new OperationException();
        }

        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/score/order/rule/update  309-修改用户订单积分规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增用户订单积分规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "self": 10000,
     *       "parent": 1000,
     *       "parent_other": 1000,
     *       "self_register": 1000,
     *       "parent_register": 1000
     *     }
     * @apiParam (请求参数说明) {int} self   用户自己获取积分比例
     * @apiParam (请求参数说明) {int} parent   用户上级获取积分比例
     * @apiParam (请求参数说明) {int} parent_other   用户上级获取附加积分比例
     * @apiParam (请求参数说明) {int} self_register   用户绑定邀请码自己获取的积分
     * @apiParam (请求参数说明) {int} parent_register   用户绑定邀请码邀请人获取的积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function scoreOrderRuleUpdate()
    {
        (new ScoreValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        $res = ScoreOrderRoleT::update($params, ['id', $params['id']]);
        if (!$res) {
            throw new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 100002
            ]);
        }

        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/score/order/rule 310-获取用户订单积分规则
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  获取用户订单积分规则
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/score/order/rule
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"self":10000,"parent":1000,"parent_other":100,"self_register":100,"parent_register":100,"create_time":"2019-01-16 14:58:57","update_time":"2019-01-16 14:58:57"}
     * @apiSuccess (返回参数说明) {int} id   规则id
     * @apiSuccess (返回参数说明) {int} self   用户自己获取积分比例
     * @apiSuccess (返回参数说明) {int} parent   用户上级获取积分比例
     * @apiSuccess (返回参数说明) {int} parent_other   用户上级获取附加积分比例
     * @apiSuccess (返回参数说明) {int} self_register   用户绑定邀请码自己获取的积分
     * @apiSuccess (返回参数说明) {int} parent_register   用户绑定邀请码邀请人获取的积分
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getScoreOrderRule()
    {
        $info = ScoreOrderRoleT::find();
        return json($info);


    }

    /**
     * @api {GET} /api/v1/score/user/list 345-获取用户积分明细列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  获取用户积分明细列表
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/score/user/list?type=1&page=1&size=10
     * @apiParam (请求参数说明) {int} type 类别：1 | 收入；2 支出
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":20,"current_page":1,"last_page":1,"data":[{"u_id":1,"score":-2000,"nickName":"盟蚁","avatarUrl":"","name_sub":null,"update_time":"2018-11-12 10:34:23","info":"积分兑换:笔记本"},{"u_id":1,"score":-10,"nickName":"盟蚁","avatarUrl":"","name_sub":null,"update_time":"2018-11-12 10:34:23","info":"积分兑换:笔记本"}],"balance":10000000}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} balance 积分余额
     * @apiSuccess (返回参数说明) {int} score 积分
     * @apiSuccess (返回参数说明) {String} update_time 时间
     * @apiSuccess (返回参数说明) {String} info 积分说明
     * @param int $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getScoreList($type = 1, $page = 1, $size = 20)
    {
        $list = (new ScoreService())->getScoreList($type, $page, $size);
        return json($list);


    }


}