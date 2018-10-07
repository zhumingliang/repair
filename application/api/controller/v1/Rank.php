<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 1:47 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\RankT;
use app\lib\exception\SuccessMessage;

class Rank extends BaseController
{
    /**
     * @api {POST} /api/v1/rank/save  49-CMS 新增/修改服务排行描述
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS 新增/修改服务排行描述
     * @apiExample {post}  请求样例:
     * {
     * "type": 1,
     * "msg": "为您提供真实的家政服务排行"
     * }
     * @apiParam (请求参数说明) {int} type 类别：1 | 家政；2 | 维修
     * @apiParam (请求参数说明) {String} msg 描述内容
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $type
     * @param $msg
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save($type, $msg)
    {
        $rank = new RankT();
        $obj = $rank->where('type', '=', $type)->find();
        if ($obj) {
            $rank->isUpdate()->save(['msg' => $msg], ['type' => $type]);
        } else {
            $data['type'] = $type;
            $data['msg'] = $msg;
            $rank->save($data);
        }

        return json(new  SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/rank/list 50-小程序/CMS获取服务排行描述列表
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  小程序/CMS获取服务排行描述列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/rank/list
     * @apiSuccessExample {json} 返回样例:
     * [{"id":6,"type":1,"msg":"为您提供真实的家政服务排行","create_time":"2018-10-08 02:13:20","update_time":"2018-10-08 02:15:31"},{"id":7,"type":2,"msg":"为您提供真实的维修服务排行","create_time":"2018-10-08 02:15:54","update_time":"2018-10-08 02:15:54"}]     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String}  type 类别：1 | 家政；2 | 维修
     * @apiSuccess (返回参数说明) {String}  msg 描述内容
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getRank()
    {
        $rank = new RankT();
        return json($rank->all());
    }


}