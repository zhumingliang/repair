<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 5:10 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\BehaviorLogT;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserInfoException;

class Behavior extends BaseController
{

    /**
     * @api {GET} /api/v1/behaviors 149-管理员-行为管理-行为日志
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员-行为管理-行为日志：点击详情的数据来自于接口返回数据
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/behaviors?page=1&size=20
     * @apiParam (请求参数说明) {int} page  当前页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":1,"u_id":4,"name":"0","ip":"127.0.0.1","create_time":"2018-10-30 17:07:30","update_time":"2018-10-30 17:07:30","user_name":"一号小区","remark":"一号小区在2018-10-30 17:07登录了后台","state":1},{"id":2,"u_id":4,"name":"用户登录","ip":"127.0.0.1","create_time":"2018-10-30 17:09:29","update_time":"2018-10-30 17:09:29","user_name":"一号小区","remark":"一号小区在2018-10-30 17:09登录了后台","state":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 日志id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} name 行为名称
     * @apiSuccess (返回参数说明) {String} user_name  执行者
     * @apiSuccess (返回参数说明) {int} create_time 执行时间
     * @apiSuccess (返回参数说明) {String} ip 登录ip
     * @apiSuccess (返回参数说明) {String} remark 备注
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getList($page = 1, $size = 20)
    {
        $list = BehaviorLogT::where('state', CommonEnum::STATE_IS_OK)
            ->paginate($size, false, ['page' => $page]);
        return json($list);


    }

    /**
     * @api {POST} /api/v1/behavior/handel  152-删除行为日志
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {POST}  请求样例:
     * {
     * "ids": '1,2,3'
     * }
     * @apiParam (请求参数说明) {String} ids  日志id列表：多个用逗号连接，ids=0 表示清空
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $ids
     * @return \think\response\Json
     * @throws UserInfoException
     */
    public function handel($ids)
    {
        if ($ids == 0) {
            $res = BehaviorLogT::update(['state' => CommonEnum::STATE_IS_FAIL], ['state' => CommonEnum::STATE_IS_OK]);
        } else {
            $res = BehaviorLogT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => [
                'in', $ids
            ]]);
        }
        if (!$res) {
            throw new UserInfoException(
                [
                    ['code' => 401,
                        'msg' => '删除失败',
                        'errorCode' => 30003
                    ]
                ]
            );

        }

        return json(new SuccessMessage());


    }

}