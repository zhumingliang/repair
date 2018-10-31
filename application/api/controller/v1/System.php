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
use app\api\model\SystemInvoiceT;
use app\api\model\SystemMsgT;
use app\api\model\SystemPhoneT;
use app\api\model\SystemShopGradeT;
use app\api\model\SystemTimeT;
use app\api\model\SystemTipT;
use app\lib\exception\SystemException;
use app\lib\exception\SuccessMessage;
use think\response\Json;

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
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 新增id
     * @param $type
     * @param $content
     * @return \think\response\Json
     * @throws SystemException
     */
    public function saveFile($type, $content)
    {
        $res = SystemMsgT::create([
            'type' => $type,
            'content' => $content
        ]);
        if (!$res->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增文档失败',
                    'errorCode' => 140002
                ]
            );
        }
        return json(['id' => $res->id]);
    }

    /**
     * @api {GET} /api/v1/system/file  115-获取指定类别文档信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定类别文档信息
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
     * @apiDescription  获取需求大厅配置列表
     * http://mengant.cn/api/v1/system/demand
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"count":20,"name":"首页所有订单循环","create_time":"2018-10-26 23:13:39","update_time":"2018-10-26 23:13:41","type":"xuqiu_all"},{"id":2,"count":50,"name":"需求大厅-维修订单显示天数","create_time":"2018-10-26 23:13:59","update_time":"2018-10-26 23:14:01","type":"xuqiu_weixiu"},{"id":3,"count":50,"name":"需求大厅-家政订单显示天数","create_time":"2018-10-26 23:14:26","update_time":"2018-10-26 23:14:28","type":"xuqiu_jiazheng"}]
     * @apiSuccess (返回参数说明) {int} id 配置ID
     * @apiSuccess (返回参数说明) {int} count 配置值
     * @apiSuccess (返回参数说明) {String} name 配置名称
     * @apiSuccess (返回参数说明) {String} type  配置类别
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

    /**
     * @api {POST} /api/v1/system/invoice/save  117-平台新增发票设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "apply": 1
     *       "top": "内容",
     *       "foot": "内容",
     *       "phone": "内容"
     *     }
     * @apiParam (请求参数说明) {String} apply  申请提示
     * @apiParam (请求参数说明) {String} top    开票说明-顶部
     * @apiParam (请求参数说明) {String} foot    开票说明-底部
     * @apiParam (请求参数说明) {String} phone    客服电话
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 新增id
     * @throws SystemException
     */
    public function saveInvoice()
    {
        $params = $this->request->param();
        $res = SystemInvoiceT::create($params);
        if (!$res->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增系统发票设置失败',
                    'errorCode' => 140005
                ]
            );
        }
        return json(['id' => $res->id]);
    }

    /**
     * @api {GET} /api/v1/system/invoice  118-获取系统设置发票信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取系统设置发票信息（返回数据为空时，点击确认按钮为新增操作/不为空则为修改操作）
     * http://mengant.cn/api/v1/invoice
     * @apiSuccessExample {json} 返回样例:
     *    {
     *       "id": 1,
     *       "apply": "内容",
     *       "top": "内容",
     *       "foot": "内容",
     *       "phone": "内容"
     *     }
     * @apiSuccess (返回参数说明) {int} id  配置ID
     * @apiSuccess (返回参数说明) {String} apply 申请提示
     * @apiSuccess (返回参数说明) {String} top  开票说明-顶部
     * @apiSuccess (返回参数说明) {String} foot 开票说明-底部
     * @apiSuccess (返回参数说明) {String} phone 客服电话
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function invoice()
    {
        $info = SystemInvoiceT::find();
        return json($info);
    }

    /**
     * @api {POST} /api/v1/system/invoice/update  119-平台修改发票设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "apply":"内容"
     *       "top": "内容",
     *       "foot": "内容",
     *       "phone": "内容"
     *     }
     * @apiParam (请求参数说明) {id} id  配置id
     * @apiParam (请求参数说明) {String} apply  申请提示
     * @apiParam (请求参数说明) {String} top    开票说明-顶部
     * @apiParam (请求参数说明) {String} foot    开票说明-底部
     * @apiParam (请求参数说明) {String} phone    客服电话
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return Json
     * @throws SystemException
     */
    public function updateInvoice()
    {
        $params = $this->request->param();
        $res = SystemInvoiceT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw new  SystemException(

                [
                    'code' => 401,
                    'msg' => '修改系统发票设置失败¬',
                    'errorCode' => 140006
                ]
            );
        }
        return json(new SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/system/tip/save  120-系统设置-新增消息提示设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "register": "注册提示"
     *       "consult": "协商提示",
     *       "no_join": "无加盟商提示"
     *     }
     * @apiParam (请求参数说明) {String} register  注册提示
     * @apiParam (请求参数说明) {String} consult    协商提示
     * @apiParam (请求参数说明) {String} no_join    无加盟商提示
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 新增id
     * @return Json
     * @throws SystemException
     */
    public function saveTip()
    {
        $params = $this->request->param();
        $res = SystemTipT::create($params);
        if (!$res->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增系统消息提示设置失败',
                    'errorCode' => 140007
                ]
            );

        }
        return json(['id' => $res->id]);
    }

    /**
     * @api {GET} /api/v1/system/tip  121-系统设置-获取消息提示设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取消息提示设置（返回数据为空时，点击确认按钮为新增操作/不为空则为修改操作）
     * http://mengant.cn/api/v1/tip
     * @apiSuccessExample {json} 返回样例:
     *    {
     *       "id": 1
     *       "register": "注册提示"
     *       "consult": "协商提示",
     *       "no_join": "无加盟商提示"
     *     }
     * @apiSuccess (返回参数说明) {int} id  配置ID
     * @apiSuccess (返回参数说明) {String} register 注册提示
     * @apiSuccess (返回参数说明) {String} consult  协商提示
     * @apiSuccess (返回参数说明) {String} no_join 无加盟商提示
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function tip()
    {
        $info = SystemTipT::find();
        return json($info);
    }

    /**
     * @api {POST} /api/v1/system/tip/update  122-平台消息提示设置-修改
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "register": "注册提示"
     *       "consult": "协商提示",
     *       "no_join": "无加盟商提示"
     *     }
     * @apiParam (请求参数说明) {id} id  配置id
     * @apiParam (请求参数说明) {String} register  注册提示
     * @apiParam (请求参数说明) {String} consult    协商提示
     * @apiParam (请求参数说明) {String} no_join    无加盟商提示
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return Json
     * @throws SystemException
     */
    public function updateTip()
    {
        $params = $this->request->param();
        $res = SystemTipT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw new  SystemException(

                [
                    'code' => 401,
                    'msg' => '修改系统发票设置失败',
                    'errorCode' => 140008
                ]
            );
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/system/time/save  123-系统设置-新增订单时间设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 时间单位（分）
     * @apiExample {post}  请求样例:
     *    {
     *       "shop_confirm": 1200
     *       "pay": 1200,
     *       "user_confirm": 1200,
     *       "consult": 1200
     *     }
     * @apiParam (请求参数说明) {int} shop_confirm  接单超时时间
     * @apiParam (请求参数说明) {int} pay    报价取消时间
     * @apiParam (请求参数说明) {int} user_confirm    自动打款时间
     * @apiParam (请求参数说明) {int} consult    协商延迟时间
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @return Json
     * @throws SystemException
     */
    public function saveOrderTime()
    {
        $params = $this->request->param();
        $res = SystemTimeT::create($params);
        if (!$res->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增系统订单时间设置失败',
                    'errorCode' => 140009
                ]
            );

        }
        return json(['id' => $res->id]);

    }

    /**
     * @api {GET} /api/v1/system/time  124-系统设置-获取订单时间设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取订单时间设置（返回数据为空时，点击确认按钮为新增操作/不为空则为修改操作）
     * http://mengant.cn/api/v1/tip
     * @apiSuccessExample {json} 返回样例:
     *    {
     *       "id": 1
     *       "shop_confirm": 1200
     *       "pay": 1200,
     *       "user_confirm": 1200,
     *       "consult": 1200
     *     }
     * @apiSuccess (返回参数说明) {int} id  配置ID
     * @apiSuccess (返回参数说明) {int} shop_confirm 注册提示
     * @apiSuccess (返回参数说明) {int} pay  协商提示
     * @apiSuccess (返回参数说明) {int} user_confirm 无加盟商提示
     * @apiSuccess (返回参数说明) {int} consult 无加盟商提示
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderTime()
    {
        $info = SystemTimeT::find();
        return json($info);
    }

    /**
     * @api {POST} /api/v1/system/time/update  125-平台修订单时间设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "shop_confirm": 1200
     *       "pay": 1200,
     *       "user_confirm": 1200,
     *       "consult": 1200
     *     }
     * @apiParam (请求参数说明) {int} id  设置id
     * @apiParam (请求参数说明) {int} shop_confirm  接单超时时间
     * @apiParam (请求参数说明) {int} pay    报价取消时间
     * @apiParam (请求参数说明) {int} user_confirm    自动打款时间
     * @apiParam (请求参数说明) {int} consult    协商延迟时间
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return Json
     * @throws SystemException
     */
    public function updateOrderTime()
    {
        $params = $this->request->param();
        $res = SystemTimeT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw new  SystemException(

                [
                    'code' => 401,
                    'msg' => '修改系统发票设置失败',
                    'errorCode' => 140010
                ]
            );
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/system/phone/save  126-系统设置-新增电话设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 时间单位（分）
     * @apiExample {post}  请求样例:
     *    {
     *       "supervise": "400-1352766"
     *       "customer": "0632-8056555"
     *     }
     * @apiParam (请求参数说明) {String} supervise 监督电话
     * @apiParam (请求参数说明) {String} customer    客服电话
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @return Json
     * @throws SystemException
     */
    public function savePhone()
    {

        $params = $this->request->param();
        $res = SystemPhoneT::create($params);
        if (!$res->id) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '新增系统电话设置失败',
                    'errorCode' => 140011
                ]
            );

        }
        return json(['id' => $res->id]);
    }

    /**
     * @api {GET} /api/v1/system/phone  127-系统设置-获取电话设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取电话设置（返回数据为空时，点击确认按钮为新增操作/不为空则为修改操作）
     * http://mengant.cn/api/v1/tip
     * @apiSuccessExample {json} 返回样例:
     *    {
     *       "id":1,
     *       "supervise": "400-1352766"
     *       "customer": "0632-8056555"
     *     }
     * @apiSuccess (返回参数说明) {int} id  配置ID
     * @apiSuccess (返回参数说明) {String} supervise 监督电话
     * @apiSuccess (返回参数说明) {String} customer 客服电话
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function phone()
    {
        $info = SystemPhoneT::find();
        return json($info);
    }

    /**
     * @api {POST} /api/v1/system/phone/update  128-平台修电话设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "supervise": "400-1352766"
     *       "customer": "0632-8056555"
     *     }
     * @apiParam (请求参数说明) {int} id  设置id
     * @apiParam (请求参数说明) {String} supervise 监督电话
     * @apiParam (请求参数说明) {String} customer    客服电话
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return Json
     * @throws SystemException
     */
    public function updatePhone()
    {
        $params = $this->request->param();
        $res = SystemPhoneT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw new  SystemException(
                [
                    'code' => 401,
                    'msg' => '修改系统电话设置失败',
                    'errorCode' => 140012
                ]
            );
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/system/shop/grade/save  135-系统设置-店铺等级设置-新增
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "grade":"1,20,30A2,30,40A3,40,50A4,50,60"
     *     }
     * @apiParam (请求参数说明) {String} grade  店铺等级设置，数据拼接格式：店铺类别，最小值，最大值A店铺类别，最小值，最大值,其中，店铺类别：1-5，5级； min-最小值；max-最大值；A为不同等级分隔符
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $grade
     * @return Json
     * @throws SystemException
     */

    public function saveShopGrade($grade)
    {
        if ($grade) {
            $grade_arr = explode('A', $grade);
            $list = array();
            foreach ($grade_arr as $k => $v) {
                $data = explode(',', $v);
                $list[] = [
                    'type' => $data[0],
                    'min' => $data[1],
                    'max' => $data[2],
                ];

            }
            $shop_grade = new SystemShopGradeT();

            $res = $shop_grade->saveAll($list);

            if (!$res) {
                throw new  SystemException(
                    [
                        'code' => 401,
                        'msg' => '新增店铺等级设置失败',
                        'errorCode' => 14017
                    ]
                );
            }
        }

        return json(new SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/system/phone  136-系统设置-获取店铺等级设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取店铺等级设置（返回数据为空时，点击确认按钮为新增操作/不为空则为修改操作）
     * http://mengant.cn/api/v1/tip
     * @apiSuccessExample {json} 返回样例:
     *    [
     * {
     *       "id":1,
     *       "type": 1
     *       "min": "400-1352766"
     *       "max": "0632-8056555"
     *     },
     * {
     *       "id":2,
     *       "type":2,
     *       "min": "400-1352766"
     *       "max": "0632-8056555"
     *     }
     * ]
     * @apiSuccess (返回参数说明) {int} id  配置ID
     * @apiSuccess (返回参数说明) {int} type 店铺等级
     * @apiSuccess (返回参数说明) {int} min 最小值
     * @apiSuccess (返回参数说明) {int} max 最大值
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopGrade()
    {
        $list = SystemShopGradeT::select();
        return json($list);

    }

    /**
     * @api {POST} /api/v1/system/shop/grade/update  137-系统设置-店铺等级设置-修改
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "grade":"1,20,30A2,30,40A3,40,50A4,50,60"
     *     }
     * @apiParam (请求参数说明) {String} grade  店铺等级设置，数据拼接格式：等级id，最小值，最大值A等级id，最小值，最大值,其中，店铺类别：1-5，5级； min-最小值；max-最大值；A为不同等级分隔符
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $grade
     * @return Json
     * @throws SystemException
     */
    public function updateShopGrade($grade)
    {

        if ($grade) {
            $grade_arr = explode('A', $grade);
            $list = array();
            foreach ($grade_arr as $k => $v) {
                $data = explode(',', $v);
                $list[] = [
                    'id' => $data[0],
                    'min' => $data[1],
                    'max' => $data[2],
                ];

            }
            $shop_grade = new SystemShopGradeT();

            $res = $shop_grade->saveAll($list);

            if (!$res) {
                throw new  SystemException(
                    [
                        'code' => 401,
                        'msg' => '新增店铺等级设置失败',
                        'errorCode' => 14017
                    ]
                );
            }
        }

        return json(new SuccessMessage());
    }


}