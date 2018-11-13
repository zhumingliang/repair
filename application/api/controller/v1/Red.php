<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/27
 * Time: 下午11:12
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\RedStrategyT;
use app\api\model\RedT;
use app\api\service\RedService;
use app\lib\enum\CommonEnum;
use app\lib\enum\RedEnum;
use app\lib\exception\RedException;
use app\lib\exception\SuccessMessage;

class Red extends BaseController
{
    /**
     * @api {GET} /api/v1/red/list 13-用户获取红包列表
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
     * "money":1
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
     * "money":1
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
     * "money":1
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
     * "money":1
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
     * "money":1
     * "detail": {
     * "id": 1,
     * "name": "首次登录"
     *
     * }
     * }
     * ]
     * @apiSuccess (返回参数说明) {int} id 红包id
     * @apiSuccess (返回参数说明) {String} create_time 红包生效时间
     * @apiSuccess (返回参数说明) {String} end_time 红包使用截止时间
     * @apiSuccess (返回参数说明) {obj} detail 红包详情对象
     * @apiSuccess (返回参数说明) {String} name 红包名称
     * @apiSuccess (返回参数说明) {int} money 红包金额
     *
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

    /**
     * @api {GET} /api/v1/red/strategy 14-小程序首页获取红包攻略
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序首页获取红包攻略
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/red/strategy
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"des":"分享功能开启，每一次您的分享都会得到系统"}]
     * @apiSuccess (返回参数说明) {int} id 红包攻略id
     * @apiSuccess (返回参数说明) {String} des 红包攻略描述
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStrategyList()
    {
        $list = RedService::getStrategyList();
        return json($list);
    }

    /**
     * @api {POST} /api/v1/strategy/save  20-后台新增红包攻略
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理新增红包攻略
     * @apiExample {post}  请求样例:
     *    {
     *       "des": "店铺首次好评将获得随机现金红包！"
     *     }
     * @apiParam (请求参数说明) {String} des    红包攻略描述
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $des
     * @return \think\response\Json
     * @throws RedException
     */
    public function saveStrategy($des)
    {
        $data = [
            'des' => $des,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $res = RedStrategyT::create($data);
        if (!$res) {
            throw  new RedException(['code' => 401,
                'msg' => '新增红包攻略失败',
                'errorCode' => 90004]);
        }

        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/strategy/update  21-后台修改红包攻略
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 管理后台修改红包攻略
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "des": "店铺首次好评将获得随机现金红包！"
     *     }
     * @apiParam (请求参数说明) {int} id    红包攻略id
     * @apiParam (请求参数说明) {String} des    红包攻略描述
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $des
     * @return \think\response\Json
     * @throws RedException
     */
    public function updateStrategy($id, $des)
    {
        $data = [
            'des' => $des
        ];
        $res = RedStrategyT::update($data, ['id' => $id]);
        if (!$res) {
            throw  new RedException(['code' => 401,
                'msg' => '更新红包攻略失败',
                'errorCode' => 90005]);
        }

        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/strategy/delete  22-后台删除红包攻略
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 管理后台删除指定红包攻略
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *     }
     * @apiParam (请求参数说明) {int} id    红包攻略id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @return \think\response\Json
     * @throws RedException
     */
    public function deleteStrategy($id)
    {
        $data = [
            'state' => CommonEnum::STATE_IS_FAIL
        ];
        $res = RedStrategyT::update($data, ['id' => $id]);
        if (!$res) {
            throw  new RedException(['code' => 401,
                'msg' => '删除红包攻略失败',
                'errorCode' => 90006]);
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/red/rule 161-CMS获取红包规则列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户获取红包列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/red/rule
     * @apiSuccessExample {json} 返回样例:
     * [{"id":4,"name":"分享红包","state":1,"money_min":2,"create_time":"2018-09-28 00:21:40","update_time":"2018-09-28 00:21:42","money_max":2,"type":4},{"id":3,"name":"店铺首次下单","state":1,"money_min":2,"create_time":"2018-09-28 00:21:03","update_time":"2018-09-28 00:21:05","money_max":5,"type":3},{"id":2,"name":"首次好评红包","state":1,"money_min":2,"create_time":"2018-09-28 00:20:32","update_time":"2018-09-28 00:20:34","money_max":2,"type":2},{"id":1,"name":"首次登录","state":1,"money_min":2,"create_time":"2018-09-28 00:20:16","update_time":"2018-09-28 00:20:18","money_max":5,"type":1}
     * @apiSuccess (返回参数说明) {int} id 红包规则id
     * @apiSuccess (返回参数说明) {String} name 红包规则名称
     * @apiSuccess (返回参数说明) {int} money_min 红包金额最小值
     * @apiSuccess (返回参数说明) {int} money_max 红包金额最大值
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function redRule()
    {
        $list = RedT::order('create_time desc')
            ->select();
        return json($list);
    }

    /**
     * @api {POST} /api/v1/red/rule/update  162-修改红包规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理新增红包攻略
     * @apiExample {post}  请求样例:
     *    {
     *       "id":1,
     *       "money_min":1,
     *       "money_max":5
     *     }
     * @apiParam (请求参数说明) {String} id    规则id
     * @apiParam (请求参数说明) {int} money_min   最小值
     * @apiParam (请求参数说明) {int} money_max    最大值
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $money_min
     * @param $money_max
     * @param $id
     * @return \think\response\Json
     * @throws RedException
     */
    public function redRuleUpdate($money_min, $money_max, $id)
    {
        $res = RedT::update(['money_min' => $money_min, 'money_max' => $money_max], ['id' => $id]);
        if (!$res) {
            throw  new RedException(['code' => 401,
                'msg' => '修改红包规则失败',
                'errorCode' => 90009]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/red/share 163-分享触发红包
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户分享获取红包
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/red/share
     * @apiSuccessExample {json} 返回样例:
     * {"money": 2}
     * @apiSuccess (返回参数说明) {int} money 红包金额
     *
     * @return \think\response\Json
     * @throws RedException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function redShare()
    {
        $money = RedService::addRed(RedEnum::SHARE, \app\api\service\Token::getCurrentUid());
        return json($money);


    }


}