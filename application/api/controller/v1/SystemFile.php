<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/26
 * Time: 11:50 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SystemMsgT;
use app\lib\exception\FileException;
use app\lib\exception\SuccessMessage;

class SystemFile extends BaseController
{
    /**
     * @api {POST} /api/v1/file/save  114-平台通用文档新增
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  （价格指导; 敏感词汇；服务条款；关于我们；用户指南）
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1
     *       "content": "内容"
     *     }
     * @apiParam (请求参数说明) {int} type    文档类别：1 | 价格指导;2 | 敏感词汇；3 | 服务条款；4 | 关于我们；5 |  用户指南
     * @apiParam (请求参数说明) {String} content    文档内容
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $type
     * @param $content
     * @return \think\response\Json
     * @throws FileException
     */
    public function save($type, $content)
    {
        $msg = SystemMsgT::create([
            'type' => $type,
            'content' => $content
        ]);
        if (!$msg->id) {
            throw new  FileException();
        }
        return json(new  SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/file  115-获取指定类别文档信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定分类信息
     * http://mengant.cn/api/v1/file?type=1
     * @apiParam (请求参数说明) {int} type    文档类别：1 | 价格指导;2 | 敏感词汇；3 | 服务条款；4 | 关于我们；5 |  用户指南
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"content":"内容"}
     * @apiSuccess (返回参数说明) {int} id 文档ID
     * @apiSuccess (返回参数说明) {String} content 文档内容
     * @param $type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function file($type)
    {
        $info = SystemMsgT::where('type', $type)->find();
        return json($info);


    }

    /**
     * @api {POST} /api/v1/file/save  116-平台通用文档修改
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改指定文档内容
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "content": "内容_修改"
     *     }
     * @apiParam (请求参数说明) {int} id    文档id
     *  * @apiParam (请求参数说明) {String} content    文档内容
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * 修改文档
     * @param $id
     * @param $content
     * @return \think\response\Json
     * @throws FileException
     */
    public function updateFile($id, $content)
    {
        $res = SystemMsgT::update([
            'content' => $content
        ], [
            'id' => $id
        ]);

        if (!$res) {
            throw new  FileException(

                [
                    'code' => 401,
                    'msg' => '修改文档失败',
                    'errorCode' => 220002
                ]

            );
        }

        return json(new  SuccessMessage());


    }

}