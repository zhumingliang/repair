<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午9:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\MessageT;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\MessageException;
use app\lib\exception\SuccessMessage;

class Message extends BaseController
{

    /**
     * @api {POST} /api/v1/message/save  9-用户给平台留言
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户给平台留言提供意见
     * @apiExample {POST}  请求样例:
     * {
     * "msg": "小程序真好用",
     * "email": "353575156@qq.com",
     * "phone": "18956225230",
     * }
     * @apiParam (请求参数说明) {String} msg  留言内容
     * @apiParam (请求参数说明) {String} email    邮箱（选填）
     * @apiParam (请求参数说明) {String} phone    联系方式
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws MessageException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        $u_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['u_id'] = $u_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $res = MessageT::create($params);
        if (!$res) {
            throw  new MessageException();
        }

        return json(new SuccessMessage());
    }
}