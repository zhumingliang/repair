<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午8:40
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\DemandService;
use  app\api\service\Token as TokenService;
use app\api\validate\DemandValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;

class Demand extends BaseController

{
    /**
     * @api {POST} /api/v1/demand/save  5-用户新增需求
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户新增需求
     * @apiExample {post}  请求样例:
     * {
     * "name": "朱明良",
     * "phone": "18956225230",
     * "des": "修马桶",
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "address": "石城大道",
     * "time_begin": "23:02:40",
     * "time_end": "23:02:43",
     * "money": "10000",
     * "type": "1",
     * "imgs": "1,2,3",
     * }
     * @apiParam (请求参数说明) {String} name 发布人
     * @apiParam (请求参数说明) {String} phone 联系方式
     * @apiParam (请求参数说明) {String} des 需求描述
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} address 详细地址
     * @apiParam (请求参数说明) {String} time_begin 开始时间
     * @apiParam (请求参数说明) {String} time_end 结束时间
     * @apiParam (请求参数说明) {int} money 金额，标准单位为分
     * @apiParam (请求参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiParam (请求参数说明) {String} imgs 图片id，多个用逗号隔开
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new DemandValidate())->goCheck();
        $u_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['u_id'] = $u_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        DemandService::save($params);
        return json(new  SuccessMessage());

    }

}