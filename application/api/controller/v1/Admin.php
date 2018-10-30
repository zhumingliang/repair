<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 1:39 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AdminJoinT;
use app\api\model\AdminT;
use app\api\validate\AdminValidate;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\AdminException;
use app\lib\exception\SuccessMessage;
use think\response\Json;

class Admin extends BaseController
{
    /**
     * @api {POST} /api/v1/admin/village/save  143-小区账户管理-新增小区账号
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商-新增小区账号
     * @apiExample {post}  请求样例:
     * {
     * "phone": "1311111111",
     * "username": "小区名称",
     * "pwd": "a111111",
     * }
     * @apiParam (请求参数说明) {String} phone 账号
     * @apiParam (请求参数说明) {String} username 用户名
     * @apiParam (请求参数说明) {String} pwd 密码
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * 新增小区账户
     * @return \think\response\Json
     * @throws AdminException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function addVillage()
    {
        (new AdminValidate())->scene('village')->goCheck();
        $params = $this->request->param();
        $params['pwd'] = sha1($params['pwd']);
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['grade'] = UserEnum::USER_MINI_VILLAGE;
        $params['parent_id'] = \app\api\service\Token::getCurrentUid();
        $admin = AdminT::create($params);
        if (!$admin) {
            throw new AdminException();

        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/circle/category/handel  145-小区账户/加盟商账户管理-用户状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  小区账户删除操作；加盟商账户删除/禁用/启用操作
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id 用户id
     * @apiParam (请求参数说明) {int} state  用户状态: 1 | 启用；2 | 禁用；3 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * 小区账户 状态修改
     * @return Json
     * @throws AdminException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel()
    {

        (new AdminValidate())->scene('handel')->goCheck();
        $id = $this->request->param('id');
        $state = $this->request->param('state');
        $res = AdminT::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw new AdminException(
                ['code' => 401,
                    'msg' => '修改用户状态失败',
                    'errorCode' => 240002
                ]
            );
        }
        return json(new  SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/admin/join/save  144-加盟商管理-新增加盟商账号
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增圈子分类
     * @apiExample {post}  请求样例:
     * {
     * "phone": "1311111111",
     * "username": "加盟商",
     * "pwd": "a111111",
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "email": "@email"
     * }
     * @apiParam (请求参数说明) {String} phone 账号
     * @apiParam (请求参数说明) {String} username 用户名
     * @apiParam (请求参数说明) {String} pwd 密码
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} email 邮箱
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * 新增加盟商
     * @return \think\response\Json
     * @throws AdminException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function addJoin()
    {
        (new AdminValidate())->scene('join')->goCheck();
        $params = $this->request->param();
        $admin['phone'] = $params['phone'];
        $admin['username'] = $params['username'];
        $admin['pwd'] = sha1($params['pwd']);
        $admin['state'] = CommonEnum::STATE_IS_OK;
        $admin['grade'] = UserEnum::USER_GRADE_JOIN;
        $admin['parent_id'] = \app\api\service\Token::getCurrentUid();
        $adminT = AdminT::create($admin);
        if (!$adminT) {
            throw new AdminException(
                ['code' => 401,
                    'msg' => '新增加盟商失败',
                    'errorCode' => 24002
                ]
            );

        }


        $join['province'] = $params['province'];
        $join['city'] = $params['city'];
        $join['area'] = $params['area'];
        $join['email'] = isset($params['email']) ? $params['email'] : '';
        $join['state'] = CommonEnum::STATE_IS_OK;
        $join['admin_id'] = $adminT->id;
        $joinT = AdminJoinT::create($join);

        if (!$joinT) {
            throw new AdminException(
                ['code' => 401,
                    'msg' => '新增加盟商关联关系失败',
                    'errorCode' => 24002
                ]
            );

        }


        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/admin/villages 145-小区账户管理-获取小区列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商-获取小区列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/admin/villages?page=1&size=20&key=""
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} key   关键字
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":4,"phone":"13111111111","username":"一号小区","update_time":"2018-10-30 10:50:55"},{"id":3,"phone":"13711111112","username":"小区账户","update_time":"2018-10-09 13:22:08"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} phone  账号
     * @apiSuccess (返回参数说明) {String} username 名称
     * @apiSuccess (返回参数说明) {String} update_time 最后登录
     * @param int $page
     * @param int $size
     * @param string $key
     * @return Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getVillageList($page = 1, $size = 20, $key = '')
    {
        $grade = \app\api\service\Token::getCurrentTokenVar('grade');
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            $list = AdminT::getVillagesForAdmin($page, $size, $key);
        } else {
            $list = AdminT::getVillagesForJoin($page, $size, $key);
        }

        return json($list);
    }

    /**
     * @api {GET} /api/v1/admin/joins 146-管理员获取加盟商列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商-获取小区列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/admin/joins?page=1&size=20&key=""
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} key   关键字
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":5,"phone":"13111111111","username":"一号小区","province":"安徽省","city":"铜陵市","area":"\b铜官区","email":"","create_time":"2018-10-30 11:00:50","state":1},{"id":2,"phone":"13711111111","username":"朱明良-加盟商","province":"安徽省","city":"铜陵市","area":"铜官区","email":"","create_time":"2018-10-02 18:43:32","state":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} phone  账号
     * @apiSuccess (返回参数说明) {String} username 名称
     * @apiSuccess (返回参数说明) {String} create_time 注册时间
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} state  状态：1 | 启用；2 | 停用
     * @param int $page
     * @param int $size
     * @param string $key
     * @return Json
     */
    public function getJoinList($page = 1, $size = 20, $key = '')
    {
        $list = AdminT::getJoins($page, $size, $key);
        return json($list);
    }

    public function getRule()
    {

    }

    public function updateRule()
    {

    }

}