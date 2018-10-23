<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/23
 * Time: 1:50 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\UnitT;

class Unit extends BaseController
{

    /**
     * @api {GET} /api/v1/units/mini 107-商家现在服务时,获取服务单位
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/units/mini
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"次"}]
     * @apiSuccess (返回参数说明) {int} id id 单位id
     * @apiSuccess (返回参数说明) {String} name 单位名称
     *
     * @return \think\response\Json
     */
    public function getUnitsForMini()
    {
        $units = UnitT::getUnitsForMini();
        return json($units);
    }

}