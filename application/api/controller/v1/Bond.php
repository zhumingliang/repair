<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:22 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\BondRecordT;
use app\api\model\BondT;
use app\api\model\ShopT;
use app\api\validate\BondValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\BondException;
use app\lib\exception\SuccessMessage;

class Bond extends BaseController
{

    /**
     * @api {POST} /api/v1/bond/save  89-新增保证金支付订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增分类
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1,
     *       "money": 500
     *     }
     * @apiParam (请求参数说明) {int} type    保证金类别：1 | 新增服务；2 | 接单
     * @apiParam (请求参数说明) {int} money    金额
     *
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 订单id
     *
     * @return array
     * @throws BondException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new BondValidate())->goCheck();
        $params = $this->request->param();
        $params['u_id'] = TokenService::getCurrentUid();
        $params['openid'] = TokenService::getCurrentOpenid();
        $params['order_number'] = makeOrderNo();
        $params['pay_id'] = CommonEnum::ORDER_STATE_INIT;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $bond = BondT::create($params);
        if (!$bond) {
            throw new BondException();
        }

        return json(['id' => $bond->id]);
    }

    /**
     * @api {POST} /api/v1/bond/operation  111-新增保证金操作记录
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增分类
     * @apiExample {post}  请求样例:
     *    {
     *       "shop_id": 1,
     *       "money": 100,
     *       "type": 1,
     *       "remark": 1
     *     }
     * @apiParam (请求参数说明) {int} shop_id   店铺id
     * @apiParam (请求参数说明) {int} type   保证金操作类别：1 | 新增；2 | 扣除
     * @apiParam (请求参数说明) {String} remark   备注
     * @apiParam (请求参数说明) {int} money    金额
     *
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 订单id
     *
     * @param $shop_id
     * @param $money
     * @param $type
     * @param string $remark
     * @return \think\response\Json
     * @throws BondException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function operation($shop_id, $money, $type, $remark = '')
    {
        $res = BondRecordT::create([
            'shop_id' => $shop_id,
            'admin_id' => TokenService::getCurrentUid(),
            'money' => $type == 1 ? $money : (0 - $money),
            'type' => $type,
            'remark' => $remark,
            'u_id' => $this->getShopUID($shop_id)
        ]);

        if (!$res->id) {
            throw new BondException(['code' => 401,
                'msg' => '保证金操作记录新增失败',
                'errorCode' => 190002
            ]);

        }
        return json(new SuccessMessage());

    }

    private function getShopUID($shop_id)
    {
        $info = ShopT::where('id', $shop_id)
            ->find();
        return $info->u_id;
    }


}