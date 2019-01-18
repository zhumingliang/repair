<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/14
 * Time: 10:53 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ScoreBuyRuleT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class ScoreBuyRole extends BaseController
{

    /**
     * @api {POST} /api/v1/score/role/save  311-新增积分充值规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "money": 100
     *       "score": 1000
     *     }
     * @apiParam (请求参数说明) {int} money   充值金额：单位分
     * @apiParam (请求参数说明) {int} score   充值积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function save()
    {
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $res = ScoreBuyRuleT::create($params);
        if (!$res) {
            throw new OperationException();
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/score/role/list 312-获取积分充值规则列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小获取积分充值规则列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/score/rule/list
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"score":1000,"money":100},{"id":2,"score":10000,"money":900}]
     * @apiSuccess (返回参数说明) {int} id 规则id
     * @apiSuccess (返回参数说明) {int} score 积分
     * @apiSuccess (返回参数说明) {int} money 金额
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = ScoreBuyRuleT::where('state', CommonEnum::STATE_IS_OK)
            ->field('id,score,money')
            ->select();
        return json($list);
    }

    /**
     * @api {POST} /api/v1/score/rule/handel  313-删除积分充值规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除积分充值规则
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1
     * }
     * @apiParam (请求参数说明) {int} id  规则id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function handel($id)
    {

        $id = ScoreBuyRuleT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$id) {
            throw new OperationException([
                'code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 100002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/score/role/update  314-修改积分充值规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "money": 100
     *       "score": 1000
     *     }
     * @apiParam (请求参数说明) {int} id  规则id
     * @apiParam (请求参数说明) {int} money   充值金额：单位分
     * @apiParam (请求参数说明) {int} score   充值积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function update()
    {
        $params = $this->request->param();
        $res = ScoreBuyRuleT::update($params);
        if (!$res) {
            throw new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 100002
            ]);
        }
        return json(new  SuccessMessage());


    }

}