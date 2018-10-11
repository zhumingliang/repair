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


    public function upload()
    {
        $file = request()->file('file');
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/static/imgs';
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        //$name = guid();
        $info = $file->move($path);
        if($info){
            return json(['name' => $info->getSaveName()]);
        }else{
            return json(['name' => $file->getError()]);
        }



    }

}