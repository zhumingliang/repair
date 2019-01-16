<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 11:09 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AddressT;
use app\api\service\AddressService;
use app\api\validate\AddressValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Address extends BaseController
{
    /**
     * @api {POST} /api/v1/address/save  300-新增用户地址
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "phone": "1895625230",
     *       "name": "朱明良",
     *       "province": "安徽省",
     *       "city": "铜陵市",
     *       "area": "铜官区",
     *       "detail": "高速地产",
     *       "type": 1,
     *     }
     * @apiParam (请求参数说明) {String} phone   手机号
     * @apiParam (请求参数说明) {String} name   姓名
     * @apiParam (请求参数说明) {String} province   省
     * @apiParam (请求参数说明) {String} city   市
     * @apiParam (请求参数说明) {String} area   区
     * @apiParam (请求参数说明) {String} detail  地址详情
     * @apiParam (请求参数说明) {String} type   是否设置为默认 1 | 默认；2| 非默认
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function save()
    {
        (new AddressValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        (new AddressService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/address/save  301-修改用户地址
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id":1,
     *       "phone": "1895625230",
     *       "name": "朱明良",
     *       "province": "安徽省",
     *       "city": "铜陵市",
     *       "area": "铜官区",
     *       "detail": "高速地产",
     *       "type": 1,
     *     }
     * @apiParam (请求参数说明) {int} id   地址id
     * @apiParam (请求参数说明) {String} phone   手机号
     * @apiParam (请求参数说明) {String} name   姓名
     * @apiParam (请求参数说明) {String} province   省
     * @apiParam (请求参数说明) {String} city   市
     * @apiParam (请求参数说明) {String} area   区
     * @apiParam (请求参数说明) {String} detail  地址详情
     * @apiParam (请求参数说明) {String} type   是否设置为默认
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function update()
    {
        (new AddressValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        (new AddressService())->update($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/address/handel  302-删除用户地址
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除用户地址
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {String} id    地址id
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function handel($id)
    {
        $res = AddressT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if ($res) {
            throw new OperationException(
                [
                    'code' => '删除操作失败',
                    'errorCode' => 60002
                ]
            );
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/address/list 303-获取用户地址列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/address/list
     * @apiSuccessExample {json} 返回样例:
     * [{"id":2,"province":"安徽省","city":"铜陵市","area":"铜官区","detail":"高速地产2","u_id":1,"type":1,"name":"朱明良","phone":"18956225230"},{"id":1,"province":"安徽省","city":"铜陵市","area":"铜官区","detail":"高速地产2","u_id":1,"type":2,"name":"朱明良","phone":"18956225230"},{"id":3,"province":"安徽省","city":"铜陵市","area":"铜官区","detail":"高速地产2","u_id":1,"type":2,"name":"朱明良","phone":"18956225230"}]
     * @apiSuccess (返回参数说明) {int} id 地址id
     * @apiSuccess (返回参数说明) {String} phone   手机号
     * @apiSuccess (返回参数说明) {String} name   姓名
     * @apiSuccess (返回参数说明) {String} province   省
     * @apiSuccess (返回参数说明) {String} city   市
     * @apiSuccess (返回参数说明) {String} area   区
     * @apiSuccess (返回参数说明) {String} detail  地址详情
     * @apiSuccess (返回参数说明) {String} type   是否设置为默认（优先显示默认地址） 1 | 默认；2| 非默认
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getList()
    {
        $u_id = \app\api\service\Token::getCurrentUid();
        $list = AddressT::getList($u_id);
        return json($list);

    }


}