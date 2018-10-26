<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/26
 * Time: 11:50 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SystemDemandT;
use app\api\model\SystemMsgT;
use app\lib\exception\SystemException;
use app\lib\exception\SuccessMessage;

class System extends BaseController
{
    /**
     * @api {POST} /api/v1/system/file/save  114-平台通用文档新增
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
     * @throws SystemException
     */
    public function saveFile($type, $content)
    {
        $msg = SystemMsgT::create([
            'type' => $type,
            'content' => $content
        ]);
        if (!$msg->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增文档失败',
                    'errorCode' => 140002
                ]
            );
        }
        return json(new  SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/system/file  115-获取指定类别文档信息
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
     * @api {POST} /api/v1/system/file/update  116-平台通用文档修改
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
     * @param $id
     * @param $content
     * @return \think\response\Json
     * @throws SystemException
     */
    public function updateFile($id, $content)
    {
        $res = SystemMsgT::update([
            'content' => $content
        ], [
            'id' => $id
        ]);

        if (!$res) {
            throw new  SystemException(

                [
                    'code' => 401,
                    'msg' => '修改文档失败',
                    'errorCode' => 140003
                ]

            );
        }

        return json(new  SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/system/demand  117-获取需求大厅配置列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定分类信息
     * http://mengant.cn/api/v1/system/demand
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"count":20,"name":"首页所有订单循环","create_time":"2018-10-26 23:13:39","update_time":"2018-10-26 23:13:41","type":"xuqiu_all"},{"id":2,"count":50,"name":"需求大厅-维修订单显示天数","create_time":"2018-10-26 23:13:59","update_time":"2018-10-26 23:14:01","type":"xuqiu_weixiu"},{"id":3,"count":50,"name":"需求大厅-家政订单显示天数","create_time":"2018-10-26 23:14:26","update_time":"2018-10-26 23:14:28","type":"xuqiu_jiazheng"}]
     * @apiSuccess (返回参数说明) {int} id 配置ID
     * @apiSuccess (返回参数说明) {int} count 配置值
     * @apiSuccess (返回参数说明) {String} name 配置名称
     * @apiSuccess (返回参数说明) {type}  配置类别
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDemand()
    {
        $list = SystemDemandT::select();

        return json($list);
    }

    /**
     * @api {POST} /api/v1/system/demand/update  116-平台需求大厅配置修改
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改指定文档内容
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "count": 10
     *     }
     * @apiParam (请求参数说明) {int} id 配置id
     * @apiParam (请求参数说明) {String} count 配置值
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @param $count
     * @throws SystemException
     */
    public function updateDemand($id, $count)
    {
        $res = SystemDemandT::update(['count' => $count], ['id' => $id]);
        if (!$res) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '修改需求大厅配置失败',
                    'errorCode' => 140004
                ]
            );
        }

    }

}