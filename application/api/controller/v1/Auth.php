<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/2
 * Time: 4:12 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AuthGroup;
use app\api\model\AuthRule;
use app\lib\enum\CommonEnum;
use app\lib\exception\AuthException;
use app\lib\exception\SuccessMessage;

class Auth extends BaseController
{
    /**
     * @api {POST} /api/v1/auth/group/save  172-权限管理-新增分组
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员-权限管理-新增分组
     * @apiExample {post}  请求样例:
     * {
     * "title": "业务组",
     * "des": "业务组的操作权限"
     * }
     * @apiParam (请求参数说明) {String} title 分组名称
     * @apiParam (请求参数说明) {String} des  描述名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @param $params
     * @return \think\response\Json
     * @throws AuthException
     */
    public function addGroup($params)
    {
        $params['state'] = CommonEnum::STATE_IS_OK;
        $res = AuthGroup::create($params);
        if (!$res->id) {
            throw  new AuthException();
        }
        return json(new SuccessMessage());
    }


    /**
     * 173-权限管理-获取分组列表
     * @return \think\response\Json
     */
    public function groups()
    {
        $list = AuthGroup::where('state', '<', CommonEnum::DELETE);
        return json($list);

    }

    /**
     * 174-权限管理-分组状态操作
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws AuthException
     */
    public function groupHandel($id, $state)
    {
        $res = AuthGroup::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '操作分组状态失败',
                'errorCode' => 250002
            ]);
        }
        return json(new SuccessMessage());

    }

    public function authRules()
    {
        //$list=
        $a = [
            '商家服务',
            '商家管理',
            '商家服务列表'
        ];
        $list[] = array();
        foreach ($a as $k => $v) {
            $list[] = [
                'title' => $v,
                'name' => $v,
                'state' => 1,
                'parent_id' => 1
            ];
        }
        $au=new AuthRule();
        $au->saveAll($list);


    }


}