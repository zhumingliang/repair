<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/2
 * Time: 4:12 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AdminT;
use app\api\model\AuthGroup;
use app\api\model\AuthGroupAccess;
use app\api\model\AuthGroupAccessV;
use app\api\model\AuthRule;
use app\api\service\AuthService;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\AdminException;
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
     * "description": "业务组的操作权限"
     * }
     * @apiParam (请求参数说明) {String} title 分组名称
     * @apiParam (请求参数说明) {String} description  描述名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $title
     * @param $description
     * @return \think\response\Json
     * @throws AuthException
     */
    public function addGroup($title, $description)
    {
        $res = AuthGroup::create([
            'title' => $title,
            'description' => $description
        ]);
        if (!$res->id) {
            throw  new AuthException();
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/auth/groups 173-权限管理-获取分组列表
     * @apiVersion 1.0.1
     * @apiDescription 获取待处理列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/auth/groups
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"title":"业务组","status":1,"rules":"14,15,16","description":null},{"id":11,"title":"前台","status":1,"rules":"","description":null},{"id":12,"title":"前台2","status":1,"rules":"","description":null},{"id":13,"title":"前台3","status":1,"rules":"","description":null},{"id":14,"title":"前台4","status":1,"rules":"","description":"哈哈"}]
     * @apiSuccess (返回参数说明) {int} id 分组ID
     * @apiSuccess (返回参数说明) {int} title 分组名称
     * @apiSuccess (返回参数说明) {int} status 分组状态：1 | 正常；2 | 停用；3 |  删除
     * @apiSuccess (返回参数说明) {int} rules 分组规则
     * @apiSuccess (返回参数说明) {string} description  分组描述
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function groups()
    {
        $list = AuthGroup::where('status', '<', CommonEnum::DELETE)->select();
        return json($list);

    }

    /**
     * @api {POST} /api/v1/auth/group/handel  174-权限管理-分组状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  权限管理-分组状态操作
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "status": 2
     * }
     * @apiParam (请求参数说明) {int} id 分组id
     * @apiParam (请求参数说明) {int} status  状态：1 | 正常；2 停用；3 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $status
     * @return \think\response\Json
     * @throws AuthException
     */
    public function groupHandel($id, $status)
    {
        $res = AuthGroup::update(['status' => $status], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '操作分组状态失败',
                'errorCode' => 250002
            ]);
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/auth/group/rule/save  175-权限管理-访问授权
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  权限管理-访问授权
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "rules": "1,2,3,4"
     * }
     * @apiParam (请求参数说明) {int} id 分组id
     * @apiParam (请求参数说明) {int} rules  权限列表，多个用逗号连接
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $rules
     * @return \think\response\Json
     * @throws AuthException
     */
    public function groupRuleSave($id, $rules)
    {
        $res = AuthGroup::update(['rules' => $rules], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '分组授权失败',
                'errorCode' => 250004
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/auth/rules 176-权限管理-获取所有分组
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/auth/rules
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * [{"id":5,"name":"商户管理","condition":"","child":[{"id":9,"name":"商家服务","condition":"","parent_id":5,"child":[{"id":12,"name":"服务推广待审核","condition":"..\/Commercial\/Service.html","parent_id":9},{"id":13,"name":"服务推广列表","condition":"..\/Commercial\/Serviced.html","parent_id":9}]},{"id":10,"name":"商家管理","condition":"","parent_id":5,"child":[{"id":14,"name":"待审核","condition":"..\/Commercial\/Store.html","parent_id":10},{"id":15,"name":"商家列表","condition":"..\/Commercial\/Stored.html","parent_id":10}]},{"id":11,"name":"商家服务列表","condition":"","parent_id":5,"child":[{"id":16,"name":"家政\/维修服务列表","condition":"..\/Commercial\/HouseKeepingMaintain.html","parent_id":11}]}]},{"id":6,"name":"设置","condition":"","child":[{"id":17,"name":"图片设置","condition":"","parent_id":6,"child":[{"id":23,"name":"首页banner","condition":"..\/Setting\/HomePageBanner.html","parent_id":17},{"id":24,"name":"引导页图片设置","condition":"..\/Setting\/BootPage.html","parent_id":17},{"id":25,"name":"加盟商轮播图管理","condition":"..\/Setting\/AllianceBanner.html","parent_id":17},{"id":26,"name":"引导页图片显示方式","condition":"..\/Setting\/BootPageDisplayType.html","parent_id":17},{"id":27,"name":"服务条款","condition":"..\/Setting\/TermService.html","parent_id":17},{"id":28,"name":"关于我们","condition":"..\/Setting\/AboutUs.html","parent_id":17},{"id":29,"name":"用户指南","condition":"..\/Setting\/Guide.html","parent_id":17},{"id":30,"name":"商家协议","condition":"..\/Setting\/BusinessAgreement.html","parent_id":17},{"id":31,"name":"需求大厅配置","condition":"..\/Setting\/DemandHall.html","parent_id":17},{"id":32,"name":"发票配置","condition":"..\/Setting\/Invoice.html","parent_id":17}]},{"id":18,"name":"红包管理","condition":"","parent_id":6,"child":[{"id":33,"name":"红包设置","condition":"..\/Setting\/RedPackage.html","parent_id":18},{"id":34,"name":"红包攻略","condition":"..\/Setting\/RedPackageStrategy.html","parent_id":18}]},{"id":19,"name":"圈子管理","condition":"","parent_id":6,"child":[{"id":36,"name":"圈子分类列表","condition":"..\/Setting\/CircleClassly.html","parent_id":19},{"id":37,"name":"圈子未审核列表","condition":"..\/Setting\/CircleUnVerify.html","parent_id":19},{"id":38,"name":"圈子审核设置","condition":"..\/Setting\/CircleVerifySetting.html","parent_id":19},{"id":39,"name":"圈子列表","condition":"..\/Setting\/Circle.html","parent_id":19}]},{"id":20,"name":"系统设置","condition":"","parent_id":6,"child":[{"id":40,"name":"消息提示设置","condition":"..\/Setting\/Message.html","parent_id":20},{"id":41,"name":"分类管理","condition":"..\/Setting\/Classify.html","parent_id":20},{"id":42,"name":"订单时间设置","condition":"..\/Setting\/OrderTime.html","parent_id":20},{"id":43,"name":"收取加盟商佣金设置","condition":"..\/Setting\/AllianceCommission.html","parent_id":20},{"id":44,"name":"电话设置","condition":"..\/Setting\/Phone.html","parent_id":20},{"id":45,"name":"店铺等级设置","condition":"..\/Setting\/StoreLevel.html","parent_id":20}]},{"id":21,"name":"价格指导","condition":"","parent_id":6,"child":[{"id":48,"name":"价格指导","condition":"..\/Setting\/PriceGuide.html","parent_id":21}]},{"id":22,"name":"单位设置","condition":"","parent_id":6,"child":[{"id":49,"name":"敏感词","condition":"..\/Setting\/Sensitive.html","parent_id":22}]}]},{"id":7,"name":"订单管理","condition":"","child":[{"id":51,"name":"导出订单管理","condition":"","parent_id":7,"child":[{"id":54,"name":"城市订单导出管理","condition":"..\/Order\/CityOrder.html","parent_id":51},{"id":55,"name":"平台订单管理","condition":"..\/Order\/PlatFormOrder.html","parent_id":51}]},{"id":52,"name":"服务订单","condition":"..\/Order\/ServiceOrder.html","parent_id":7,"child":[]},{"id":53,"name":"需求订单","condition":"..\/Order\/RequireOrder.html","parent_id":7,"child":[]}]},{"id":8,"name":"用户","condition":"","child":[{"id":56,"name":"用户管理","condition":"","parent_id":8,"child":[{"id":63,"name":"用户信息","condition":"..\/User\/UserInformation.html","parent_id":56},{"id":64,"name":"权限管理","condition":"..\/User\/Authority.html","parent_id":56}]},{"id":57,"name":"行为管理","condition":"","parent_id":8,"child":[{"id":65,"name":"用户行为","condition":"..\/User\/UserBehavior.html","parent_id":57}]},{"id":58,"name":"加盟商管理","condition":"","parent_id":8,"child":[{"id":66,"name":"加盟商列表","condition":"..\/User\/Alliance.html","parent_id":58},{"id":67,"name":"加盟商提现列表","condition":"..\/User\/AllianceWithdrow.html","parent_id":58},{"id":68,"name":"加盟商提现完成","condition":"..\/User\/AllianceWithdrowFinish.html","parent_id":58}]},{"id":59,"name":"小区账户管理","condition":"","parent_id":8,"child":[{"id":69,"name":"小区用户","condition":"..\/User\/AreaUser.html","parent_id":59}]},{"id":60,"name":"提现管理","condition":"","parent_id":8,"child":[{"id":70,"name":"提现申请列表","condition":"..\/User\/AllianceWithdrow.html","parent_id":60},{"id":71,"name":"提现完成列表","condition":"..\/User\/AllianceWithdrowFinish.html","parent_id":60}]},{"id":61,"name":"反馈管理","condition":"","parent_id":8,"child":[{"id":72,"name":"反馈未查看","condition":"..\/User\/FeedBack.html","parent_id":61},{"id":73,"name":"反馈已查看列表","condition":"..\/User\/FeedBacked.html","parent_id":61}]},{"id":74,"name":"平台推广","condition":"","parent_id":8,"child":[{"id":75,"name":"服务优惠设置","condition":"..\/Setting\/Circle.html","parent_id":74}]}]}]
     * @apiSuccess (返回参数说明) {int} id 规则id
     * @apiSuccess (返回参数说明) {int} name 规则名称
     * @apiSuccess (返回参数说明) {Obj} child 子集菜单
     * @apiSuccess (返回参数说明) {int} rules 分组规则
     * @apiSuccess (返回参数说明) {string} condition  路径
     *
     * @return \think\response\Json
     */
    public function authRules()
    {
        $rules = (new AuthService())->authRules();
        return json($rules);
    }

    /**
     * @api {GET} /api/v1/auth/group/rules   177-权限管理-获取分组权限
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/auth/group/rules
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * [{"id":5,"name":"商户管理","condition":"","child":[{"id":9,"name":"商家服务","condition":"","parent_id":5,"child":[{"id":12,"name":"服务推广待审核","condition":"..\/Commercial\/Service.html","parent_id":9},{"id":13,"name":"服务推广列表","condition":"..\/Commercial\/Serviced.html","parent_id":9}]},{"id":10,"name":"商家管理","condition":"","parent_id":5,"child":[{"id":14,"name":"待审核","condition":"..\/Commercial\/Store.html","parent_id":10},{"id":15,"name":"商家列表","condition":"..\/Commercial\/Stored.html","parent_id":10}]},{"id":11,"name":"商家服务列表","condition":"","parent_id":5,"child":[{"id":16,"name":"家政\/维修服务列表","condition":"..\/Commercial\/HouseKeepingMaintain.html","parent_id":11}]}]},{"id":6,"name":"设置","condition":"","child":[{"id":17,"name":"图片设置","condition":"","parent_id":6,"child":[{"id":23,"name":"首页banner","condition":"..\/Setting\/HomePageBanner.html","parent_id":17},{"id":24,"name":"引导页图片设置","condition":"..\/Setting\/BootPage.html","parent_id":17},{"id":25,"name":"加盟商轮播图管理","condition":"..\/Setting\/AllianceBanner.html","parent_id":17},{"id":26,"name":"引导页图片显示方式","condition":"..\/Setting\/BootPageDisplayType.html","parent_id":17},{"id":27,"name":"服务条款","condition":"..\/Setting\/TermService.html","parent_id":17},{"id":28,"name":"关于我们","condition":"..\/Setting\/AboutUs.html","parent_id":17},{"id":29,"name":"用户指南","condition":"..\/Setting\/Guide.html","parent_id":17},{"id":30,"name":"商家协议","condition":"..\/Setting\/BusinessAgreement.html","parent_id":17},{"id":31,"name":"需求大厅配置","condition":"..\/Setting\/DemandHall.html","parent_id":17},{"id":32,"name":"发票配置","condition":"..\/Setting\/Invoice.html","parent_id":17}]},{"id":18,"name":"红包管理","condition":"","parent_id":6,"child":[{"id":33,"name":"红包设置","condition":"..\/Setting\/RedPackage.html","parent_id":18},{"id":34,"name":"红包攻略","condition":"..\/Setting\/RedPackageStrategy.html","parent_id":18}]},{"id":19,"name":"圈子管理","condition":"","parent_id":6,"child":[{"id":36,"name":"圈子分类列表","condition":"..\/Setting\/CircleClassly.html","parent_id":19},{"id":37,"name":"圈子未审核列表","condition":"..\/Setting\/CircleUnVerify.html","parent_id":19},{"id":38,"name":"圈子审核设置","condition":"..\/Setting\/CircleVerifySetting.html","parent_id":19},{"id":39,"name":"圈子列表","condition":"..\/Setting\/Circle.html","parent_id":19}]},{"id":20,"name":"系统设置","condition":"","parent_id":6,"child":[{"id":40,"name":"消息提示设置","condition":"..\/Setting\/Message.html","parent_id":20},{"id":41,"name":"分类管理","condition":"..\/Setting\/Classify.html","parent_id":20},{"id":42,"name":"订单时间设置","condition":"..\/Setting\/OrderTime.html","parent_id":20},{"id":43,"name":"收取加盟商佣金设置","condition":"..\/Setting\/AllianceCommission.html","parent_id":20},{"id":44,"name":"电话设置","condition":"..\/Setting\/Phone.html","parent_id":20},{"id":45,"name":"店铺等级设置","condition":"..\/Setting\/StoreLevel.html","parent_id":20}]},{"id":21,"name":"价格指导","condition":"","parent_id":6,"child":[{"id":48,"name":"价格指导","condition":"..\/Setting\/PriceGuide.html","parent_id":21}]},{"id":22,"name":"单位设置","condition":"","parent_id":6,"child":[{"id":49,"name":"敏感词","condition":"..\/Setting\/Sensitive.html","parent_id":22}]}]},{"id":7,"name":"订单管理","condition":"","child":[{"id":51,"name":"导出订单管理","condition":"","parent_id":7,"child":[{"id":54,"name":"城市订单导出管理","condition":"..\/Order\/CityOrder.html","parent_id":51},{"id":55,"name":"平台订单管理","condition":"..\/Order\/PlatFormOrder.html","parent_id":51}]},{"id":52,"name":"服务订单","condition":"..\/Order\/ServiceOrder.html","parent_id":7,"child":[]},{"id":53,"name":"需求订单","condition":"..\/Order\/RequireOrder.html","parent_id":7,"child":[]}]},{"id":8,"name":"用户","condition":"","child":[{"id":56,"name":"用户管理","condition":"","parent_id":8,"child":[{"id":63,"name":"用户信息","condition":"..\/User\/UserInformation.html","parent_id":56},{"id":64,"name":"权限管理","condition":"..\/User\/Authority.html","parent_id":56}]},{"id":57,"name":"行为管理","condition":"","parent_id":8,"child":[{"id":65,"name":"用户行为","condition":"..\/User\/UserBehavior.html","parent_id":57}]},{"id":58,"name":"加盟商管理","condition":"","parent_id":8,"child":[{"id":66,"name":"加盟商列表","condition":"..\/User\/Alliance.html","parent_id":58},{"id":67,"name":"加盟商提现列表","condition":"..\/User\/AllianceWithdrow.html","parent_id":58},{"id":68,"name":"加盟商提现完成","condition":"..\/User\/AllianceWithdrowFinish.html","parent_id":58}]},{"id":59,"name":"小区账户管理","condition":"","parent_id":8,"child":[{"id":69,"name":"小区用户","condition":"..\/User\/AreaUser.html","parent_id":59}]},{"id":60,"name":"提现管理","condition":"","parent_id":8,"child":[{"id":70,"name":"提现申请列表","condition":"..\/User\/AllianceWithdrow.html","parent_id":60},{"id":71,"name":"提现完成列表","condition":"..\/User\/AllianceWithdrowFinish.html","parent_id":60}]},{"id":61,"name":"反馈管理","condition":"","parent_id":8,"child":[{"id":72,"name":"反馈未查看","condition":"..\/User\/FeedBack.html","parent_id":61},{"id":73,"name":"反馈已查看列表","condition":"..\/User\/FeedBacked.html","parent_id":61}]},{"id":74,"name":"平台推广","condition":"","parent_id":8,"child":[{"id":75,"name":"服务优惠设置","condition":"..\/Setting\/Circle.html","parent_id":74}]}]}]
     * @apiSuccess (返回参数说明) {int} id 规则id
     * @apiSuccess (返回参数说明) {int} name 规则名称
     * @apiSuccess (返回参数说明) {Obj} child 子集菜单
     * @apiSuccess (返回参数说明) {int} rules 分组规则
     * @apiSuccess (返回参数说明) {string} condition  路径
     *
     * @param $id
     * @return \think\response\Json
     */
    public function groupRules($id)
    {
        $rules = (new AuthService())->getGroupRules($id);
        return json($rules);

    }

    /**
     * @api {POST} /api/v1/auth/group/user/save   178-权限管理-成员授权-新增
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员-权限管理-新增分组
     * @apiExample {post}  请求样例:
     * {
     * "group_id": 1,
     * "u_id": "4,5"
     * }
     * @apiParam (请求参数说明) {int} group_id 分组ID
     * @apiParam (请求参数说明) {String} u_id  用户id,多个用逗号连接
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $u_id
     * @param $group_id
     * @return \think\response\Json
     * @throws AuthException
     */
    public function userGroup($u_id, $group_id)
    {
        $u_id_arr = explode(',', $u_id);
        $list[] = array();
        for ($i = 0; $i < count($u_id_arr); $i++) {

            $list[$i] = [
                'uid' => $u_id_arr[$i],
                'group_id' => $group_id,
                'status' => CommonEnum::STATE_IS_OK
            ];
        }

        $access = new AuthGroupAccess();
        $res = $access->saveAll($list);
        if (!$res) {
            throw  new AuthException(['code' => 401,
                'msg' => '成员授权失败',
                'errorCode' => 250008]);
        }

        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/group/user/handel  179-权限管理-成员授权解除
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  权限管理-分组状态操作
     * @apiExample {post}  请求样例:
     * {
     * "id": 1
     * }
     * @apiParam (请求参数说明) {int} id 分组-用户关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @return \think\response\Json
     * @throws AuthException
     */
    public
    function deleteUserGroup($id)
    {
        $res = AuthGroupAccess::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '解除授权失败',
                'errorCode' => 250008
            ]);
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/auth/group/users 180-权限管理-获取分组成员
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/auth/group/users?page=1&size=20&id=1
     * @apiParam (请求参数说明) {int} id 分组id
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"10","current_page":1,"last_page":1,"data":[{"id":21,"group_id":1,"username":"一号小区","login_count":0,"update_time":"2018-10-30 10:50:55"},{"id":22,"group_id":1,"username":"一号小区","login_count":0,"update_time":"2018-10-30 11:00:50"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 用户-分组关联id
     * @apiSuccess (返回参数说明) {String} username 用户昵称
     * @apiSuccess (返回参数说明) {int} login_count 登录次数
     * @apiSuccess (返回参数说明) {String} update_time 最后登录时间
     * @param $id
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public
    function groupUsers($id, $page, $size)
    {
        $list = AuthGroupAccessV::where('group_id', $id)
            ->order('id')
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }


    /**
     * @api {POST} /api/v1/admin/save  181-用户管理-新增用户
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商-新增小区账号
     * @apiExample {post}  请求样例:
     * {
     * "phone": "1311111111",
     * "pwd": "a111111",
     * "email": "a111111",
     * }
     * @apiParam (请求参数说明) {String} phone 用户名
     * @apiParam (请求参数说明) {String} pwd 密码
     * @apiParam (请求参数说明) {String} email 邮箱
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public
    function addAdmin()
    {
        $params = $this->request->param();
        $params['username'] = $params['phone'];
        $params['pwd'] = sha1($params['pwd']);
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['grade'] = UserEnum::USER_GRADE_ADMIN;
        $params['parent_id'] = \app\api\service\Token::getCurrentUid();
        $admin = AdminT::create($params);
        if (!$admin) {
            throw new AdminException();

        }
        return json(new SuccessMessage());

    }


}