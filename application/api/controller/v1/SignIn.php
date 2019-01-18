<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/18
 * Time: 11:45 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SystemSignInT;
use app\api\validate\SignInValidate;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class SignIn extends BaseController
{

    /**
     * @api {POST} /api/v1/system/sign/in/save  338-新增签到规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增签到规则
     * @apiExample {post}  请求样例:
     *    {
     *       "cycle":7,
     *       "begin": 1,
     *       "begin_score": 1000,
     *       "add": 10
     *     }
     * @apiParam (请求参数说明) {int} cycle   循环周期
     * @apiParam (请求参数说明) {int} begin   固定起始日:1-7 :星期一～星期天
     * @apiParam (请求参数说明) {int} begin_score   起始积分
     * @apiParam (请求参数说明) {int} add   每日增加积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function save()
    {
        (new SignInValidate())->scene('system_save')->goCheck();
        $params = $this->request->param();
        $res = SystemSignInT::create($params);
        if (!$res) {
            throw  new OperationException();
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/system/sign/in/update  339-修改签到规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id":1,
     *       "cycle":7,
     *       "begin": 1,
     *       "begin_score": 1000,
     *       "add": 10
     *     }
     * @apiParam (请求参数说明) {int} id   规则id
     * @apiParam (请求参数说明) {int} cycle   循环周期
     * @apiParam (请求参数说明) {int} begin   固定起始日:1-7 :星期一～星期天
     * @apiParam (请求参数说明) {int} begin_score   起始积分
     * @apiParam (请求参数说明) {int} add   每日增加积分
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function update()
    {
        (new SignInValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        $res = SystemSignInT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 160011
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/system/sign/in 340-获取签到规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取签到规则
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/system/sign/in
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"cycle":7,"begin":1,"begin_score":1000,"add":10}     * @apiSuccess (返回参数说明) {int} id   规则id
     * @apiSuccess (返回参数说明) {int} id   规则id
     * @apiSuccess (返回参数说明) {int} cycle   循环周期
     * @apiSuccess (返回参数说明) {int} begin   固定起始日:1-7 :星期一～星期天
     * @apiSuccess (返回参数说明) {int} begin_score   起始积分
     * @apiSuccess (返回参数说明) {int} add   每日增加积分
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSignInRole()
    {
        $info = SystemSignInT::field('id,cycle,begin,begin_score,add')->find();
        return json($info);
    }

    public function signIn()
    {

    }

}