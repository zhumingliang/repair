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
     * @api {POST} /api/v1/message/save  23-用户给平台留言
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

    /**
     * @api {GET} /api/v1/message/list 146-管理员-反馈管理
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员反馈管理列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/message/list?state=1&page=1&size=20
     * @apiParam (请求参数说明) {int} state  列表类别
     * @apiParam (请求参数说明) {int} page  当前页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":9,"per_page":"1","current_page":1,"last_page":9,"data":[{"id":1,"msg":"陈独秀","email":"993053701@qq.com","phone":"18219112831","state":1,"create_time":"2018-10-15 21:36:28","update_time":"2018-10-15 21:36:28","u_id":5}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 反馈id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} create_time  反馈时间
     * @apiSuccess (返回参数说明) {String} phone 电话
     * @apiSuccess (返回参数说明) {String} msg 内容
     * @apiSuccess (返回参数说明) {int} state  状态：1 | 未查看；2 | 已查看
     * @param int $page
     * @param int $size
     * @param int $state
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getMessages($page = 1, $size = 20, $state = 1)
    {
        $list = MessageT::where('state', $state)
            //->hidden()
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }

    /**
     * @api {POST} /api/v1/message/handel  147-反馈消息状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  查看/删除
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id 用户id
     * @apiParam (请求参数说明) {int} state  用户状态: 2 | 查看；3 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws MessageException
     */
    public function handel($id, $state)
    {
        $res = MessageT::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw  new MessageException(['code' => 401,
                'msg' => '状态操作失败',
                'errorCode' => 70002
            ]);
        }
        return json(new  SuccessMessage());

    }
}