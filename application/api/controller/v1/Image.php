<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:07
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ImgT;
use app\api\model\VillageRecordT;
use app\api\service\ImageService;
use app\api\validate\ImageValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\ImageException;
use app\lib\exception\SuccessMessage;
use WxMsg\WXBizDataCrypt;

class Image extends BaseController
{
    /**
     * @api {POST} /api/v1/image/save  19-图片上传
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "img": "4f4bc4dec97d474b"
     *     }
     * @apiParam (请求参数说明) {String} img    图片base64位编码
     *
     * @apiSuccessExample {json} 返回样例:
     *{"id":17}
     * @apiSuccess (返回参数说明) {int} id 图片id
     *
     * @param $img
     * @return \think\response\Json
     * @throws ImageException
     */
    public function save($img)
    {
        $param['url'] = base64toImg($img);
        $param['state'] = CommonEnum::STATE_IS_OK;
        $obj = ImgT::create($param);
        if (!$obj) {
            throw new ImageException();
        }

        return json(['id' => $obj->id]);

    }

    /**
     * @api {POST} /api/v1/image/upload  72-接受小程序推送图片并保存
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiSuccessExample {json} 返回样例:
     *{"id":17}
     * @apiSuccess (返回参数说明) {int} id 图片id
     */
    public function upload()
    {
        //(new ImageValidate())->goCheck();
        $file = request()->file('file');
        $res = ImageService::saveImageFromWX($file);
        return json([
            'id' => $res
        ]);
    }

    /**
     * @api {POST} /api/v1/image/search  73-接受小程序小区管理员推送图片并识别订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "city": "铜陵市"
     *     }
     * @apiParam (请求参数说明) {String} file  图片文件
     * @apiParam (请求参数说明) {String} city  用户所在市
     * @apiSuccessExample {json} 返回样例:
     * {"orders":[{"shop_id":1,"order_name":"修马桶","username":"朱明良","area":"铜官山区","address":"高速","time_begin":"2018-10-17 08:00:00","time_end":"2018-10-01 12:00:00","order_id":1,"type":1}],"shop_info":{"name":"修之家","area":"铜官区","address":"","phone":18956225230}}
     * @apiSuccess (返回参数说明) {String} orders 订单列表
     * @apiSuccess (返回参数说明) {int} shop_id 店铺id
     * @apiSuccess (返回参数说明) {int} order_name 订单名称
     * @apiSuccess (返回参数说明) {String} username 用户名称
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {String} time_begin 开始时间
     * @apiSuccess (返回参数说明) {String} time_end 结束时间
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {int} type 订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccess (返回参数说明) {Obj} shop_info 店铺信息
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {String} phone 联系电话
     */
    public function search()
    {
        //(new ImageValidate())->goCheck();
        $file = request()->file('file');
        $city = $this->request->param('city');
        $orders = ImageService::staffSearch($file, md5($city));
        return json($orders);
    }




}