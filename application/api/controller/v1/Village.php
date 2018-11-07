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
use app\api\model\VillageRecordV;
use app\api\service\OrderReportService;
use app\lib\enum\UserEnum;
use app\lib\exception\ImageException;
use app\lib\exception\SuccessMessage;
use \app\api\service\Token as TokenService;

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
        $params['admin_id'] = TokenService::getCurrentTokenVar('v_id');
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
     * @api {GET} /api/v1/village/list 156-小区管理员-获取进入小区记录列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  小区管理员获取列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/village/list/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"1","current_page":1,"last_page":1,"data":[{"admin_id":3,"shop_id":11,"shop_name":"李福招的店铺哦","order_type":"维修服务","phone":"18219112831","money":1,"create_time":"2018-10-31 15:36:14"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} shop_id 商家id
     * @apiSuccess (返回参数说明) {String} shop_name  店铺名称
     * @apiSuccess (返回参数说明) {String} order_type 服务类别
     * @apiSuccess (返回参数说明) {String} phone 商家电话
     * @apiSuccess (返回参数说明) {int} money 金额
     * @apiSuccess (返回参数说明) {String} create_time 时间
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getList($page, $size)
    {
        $id = TokenService::getCurrentUid();
        $list = VillageRecordV::where('admin_id', $id)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/village/export 157-加盟商/小区管理员导出小区记录管理
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 直接访问连接下载数据
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/village/export?time_begin=2018-08-01&time_end=2018-10-30
     * @apiParam (请求参数说明) {String} time_begin 开始时间
     * @apiParam (请求参数说明) {String} time_end 结束时间
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function exportVillageRecord()
    {
        $token = $this->request->param('token');
        $time_begin = $this->request->param('time_begin');
        echo $time_begin;
        $time_end = $this->request->param('time_end');

        $grade = TokenService::getCurrentTokenVarWithToken('grade', $token);
        if ($grade == UserEnum::USER_GRADE_JOIN) {

            $province = TokenService::getCurrentTokenVarWithToken('province', $token);
            $city = TokenService::getCurrentTokenVarWithToken('city', $token);
            $area = TokenService::getCurrentTokenVarWithToken('area', $token);
            $sql = preJoinSqlForGetDShops($province, $city, $area);

            $list = VillageRecordV:: whereRaw($sql)
                ->whereTime('create_time', 'between', [$time_begin, $time_end])
                ->field('shop_id,shop_name,order_type,phone,money,create_time')
                ->select();
        } else {
            $list = VillageRecordV::where('admin_id', TokenService::getCurrentTokenVarWithToken('u_id', $token))
                ->whereTime('create_time', 'between', [$time_begin, $time_end])
                ->field('shop_id,shop_name,order_type,phone,money,create_time')
                ->select()->toArray();

        }

        $header = array(
            '商户id',
            '店铺名称',
            '服务类型',
            '商家电话',
            '价格',
            '进入时间'
        );
        $file_name = '小区进入记录' . '-' . date('Y-m-d', time()) . '.csv';
        (new OrderReportService())->put_csv($list, $header, $file_name);

    }


}