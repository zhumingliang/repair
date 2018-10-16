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
use app\api\service\ImageService;
use app\api\validate\ImageValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\ImageException;

class Image extends BaseController
{
    /**
     * @api {POST} /api/v1/image/save  19-图片上传
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "base64": "4f4bc4dec97d474b"
     *     }
     * @apiParam (请求参数说明) {String} base64    图片base64位编码
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
     * @apiSuccessExample {json} 返回样例:
     *{"id":17}
     * @apiSuccess (返回参数说明) {int} id 图片id
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