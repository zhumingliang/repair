<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/19
 * Time: 12:02 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\VillageRecordT;
use app\lib\exception\ImageException;
use app\lib\exception\SuccessMessage;

class Village extends BaseController
{
    /**
     * @api {POST} /api/v1/village/confirm  96-小区管理员新增服务记录
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "o_id": 1,
     *       "type": 1,
     *     }
     * @apiParam (请求参数说明) {int} o_id    订单id
     * @apiParam (请求参数说明) {int} type    订单类别：1 | 服务订单；2| 需求订单
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws ImageException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function villageConfirm()
    {
        $params = $this->request->param();
        $params['admin_id'] = \app\api\service\Token::getCurrentUid();
        $record = VillageRecordT::create($params);
        if (!$record->id) {
            throw new ImageException(['code' => 401,
                'msg' => '新增服务记录失败',
                'errorCode' => 210001
            ]);
        }

        return json(new SuccessMessage());

    }

    /**
     * 小区管理员获取列表
     * @param $page
     * @param $size
     */
    public function getList($page, $size)
    {
        $id = \app\api\service\Token::getCurrentUid();


    }


}