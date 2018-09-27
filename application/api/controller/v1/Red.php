<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/27
 * Time: 下午11:12
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\RedService;

class Red extends BaseController
{
    /**
     * @api {GET} /api/v1/collection/list 13-用户获取红包列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户获取红包列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/red/list
     * @apiSuccessExample {json} 返回样例:
     * [
     * {
     * "id": 5,
     * "r_id": 2,
     * "create_time": "2018-09-28",
     * "end_time": "2018-10-28",
     * "detail": {
     * "id": 2,
     * "name": "首次好评红包"
     * }
     * },
     * {
     * "id": 4,
     * "r_id": 4,
     * "create_time": "2018-09-28",
     * "end_time": "2018-10-28",
     * "detail": {
     * "id": 4,
     * "name": "分享红包"
     * }
     * },
     * {
     * "id": 3,
     * "r_id": 3,
     * "create_time": "2018-09-28",
     * "end_time": "2018-10-28",
     * "detail": {
     * "id": 3,
     * "name": "店铺首次下单"
     * }
     * },
     * {
     * "id": 2,
     * "r_id": 2,
     * "create_time": "2018-09-28",
     * "end_time": "2018-10-28",
     * "detail": {
     * "id": 2,
     * "name": "首次好评红包"
     * }
     * },
     * {
     * "id": 1,
     * "r_id": 1,
     * "create_time": "2018-09-28",
     * "end_time": "2018-10-28",
     * "detail": {
     * "id": 1,
     * "name": "首次登录"
     * }
     * }
     * ]
     * @apiSuccess (返回参数说明) {int} id 红包id
     * @apiSuccess (返回参数说明) {String} create_time 红包生效时间
     * @apiSuccess (返回参数说明) {String} end_time 红包使用截止时间
     * @apiSuccess (返回参数说明) {obj} detail 红包详情对象
     * @apiSuccess (返回参数说明) {String} name 红包名称
     * @apiSucce
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = RedService::getList();
        return json($list);
    }


}