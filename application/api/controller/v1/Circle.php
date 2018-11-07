<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:17 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CircleCategoryT;
use app\api\model\CircleExamineT;
use app\api\model\CircleT;
use app\api\service\CircleService;
use app\api\service\ImageService;
use app\api\validate\CircleValidate;
use app\api\validate\PagingParameter;
use app\lib\enum\CommonEnum;
use app\lib\exception\CircleException;
use app\lib\exception\SuccessMessage;


class Circle extends BaseController
{

    /**
     * @api {POST} /api/v1/circle/category/save  50-CMS新增圈子类别
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增圈子分类
     * @apiExample {post}  请求样例:
     * {
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "name": "失物招领"
     * }
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} name 分类名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return SuccessMessage
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function saveCategory()
    {
        (new CircleValidate())->scene('category_save')->goCheck();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = CircleCategoryT::create($params);
        if (!$id) {
            throw  new CircleException();
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/circle/category/handel  51-圈子类别状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除圈子类别
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 类别id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws \app\lib\exception\ParameterException
     * @throws CircleException
     */
    public function categoryHandel()
    {
        (new CircleValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        $id = CircleCategoryT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new CircleException(
                [
                    'code' => 401,
                    'msg' => '操作圈子类别状态失败',
                    'errorCode' => 160002
                ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/circle/cms/category/list 52-CMS获取圈子类别列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取圈子类别列表（管理员-圈子分类列表/加盟商-新增圈子时获取分类列表）
     *
     * @apiExample {get}  管理员-圈子分类列表,请求样例:
     * http://mengant.cn/api/v1/circle/category/list?page=1&size=20
     * @apiExample {get}  加盟商-新增圈子时获取分类列表,请求样例:
     * http://mengant.cn/api/v1/circle/category/list
     * @apiSuccessExample {json} 管理员-圈子分类列表,返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"province":"安徽省","city":"铜陵市","area":"铜官区","name":"失物招领","create_time":"-0001-11-30 00:00:00"}]}
     * @apiSuccessExample {json} 加盟商-新增圈子时获取分类列表,返回样例:
     * [{"id":1,"name":"失物招领"}]
     *
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 类别id
     * @apiSuccess (返回参数说明) {int} name  类别名称
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryListForCms()
    {
        $params = $this->request->param();
        $list = CircleService::getCategoryListForCms($params);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/circle/mini/category/list 55-小程序获取圈子类别列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取圈子类别列表（圈子模块）
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/category/list?province=安徽省&city=铜陵市&area=铜官区
     * @apiParam (请求参数说明) {int}  province 用户地理位置-省
     * @apiParam (请求参数说明) {int}  city 用户地理位置-市
     * @apiParam (请求参数说明) {int}  area 用户地理位置-区
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"失物招领"}]
     * @apiSuccess (返回参数说明) {int} id 类别id
     * @apiSuccess (返回参数说明) {int} name  类别名称
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryListForMini()
    {
        $params = $this->request->param();
        $list = CircleService::getCategoryListForMini($params);
        return json($list);
    }

    /**
     * @api {POST} /api/v1/circle/pass/set  53-修改圈子审核设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改圈子审核设置
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "type": 1
     *     }
     * @apiParam (请求参数说明) {int} id    设置id
     * @apiParam (请求参数说明) {int} type  设置类别：1 | 默认通过；2 | 默认需要审核
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function circlePassSet()
    {
       // (new CircleValidate())->scene('set')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $id = CircleExamineT::update(['default' => $type], ['id' => $id]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '操作圈子类别状态失败',
                'errorCode' => 160002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/circle/pass/get 54-获取圈子审核设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取圈子审核设置
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/pass/get
     * @apiSuccessExample {json} 返回样例:
     * {"id":2,"default":1,"create_time":"2018-10-08 23:45:14","update_time":"2018-10-08 23:45:14"}
     * @apiSuccess (返回参数说明) {int} id 设置id
     * @apiSuccess (返回参数说明) {int} default  设置类别：1 | 默认通过；2 | 默认需要审核
     *
     * @return \think\response\Json
     * @throws CircleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCirclePassSet()
    {
        $examine = new CircleExamineT();
        $obj = $examine->find();
        if (!$obj) {
            $examine->default = 1;
            $obj = $examine->save();
            if (!$obj) {
                throw new CircleException(['code' => 401,
                    'msg' => '新增圈子默认设置失败',
                    'errorCode' => 160003
                ]);
            }
            return json($examine);
        }

        return json($obj);

    }

    /**
     * @api {POST} /api/v1/circle/save  56-CMS新增圈子文章
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  加盟商/小区管理员-新增圈子文章
     * @apiExample {post}  请求样例:
     * {
     * "title": "你的睡眠真的好吗？",
     * "head_img": 1,
     * "content": "每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害",
     * "c_id": 1
     * }
     * @apiParam (请求参数说明) {String} title 文章标题
     * @apiParam (请求参数说明) {String} head_img 文章封面图id
     * @apiParam (请求参数说明) {String} content 文章内容
     * @apiParam (请求参数说明) {int} c_id 分类id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveCircle()
    {
        (new CircleValidate())->scene('circle_save')->goCheck();
        $params = $this->request->param();
        CircleService::saveCircle($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/circle/handel  57-CMS圈子文章状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除/审核通过
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id  圈子id
     * @apiParam (请求参数说明) {String} state   状态类别：2 审核通过；3 | 拒绝；4 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel()
    {
        (new CircleValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        $id = CircleT::update(['state' => $params['state']], ['id' => $params['id']]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '圈子文章状态修改失败',
                'errorCode' => 160005
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/circle/top/handel  58-CMS圈子文章置顶状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除/审核通过
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "top":2
     * }
     * @apiParam (请求参数说明) {int} id  圈子id
     * @apiParam (请求参数说明) {int} top   置顶状态：1 | 不置顶；2 | 置顶
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function topHandel()
    {
        (new CircleValidate())->scene('top_handel')->goCheck();
        $params = $this->request->param();
        $id = CircleT::update(['top' => $params['top']], ['id' => $params['id']]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '圈子文章置顶状态修改失败',
                'errorCode' => 160006
            ]);
        }

    }

    /**
     * @api {GET} /api/v1/circle/cms/list 59-CMS获取圈子列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取圈子列表(已经审核/未审核)
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/category/list?page=1&size=20&type=1
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} type   类别：1 | 待审核；2 | 已审核
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":5,"create_time":"2018-10-09 22:55:23","state":1,"top":1,"city":"铜陵市","title":"睡觉2","category":{"id":1,"name":"保姆"}},{"id":4,"create_time":"2018-10-09 22:34:18","state":1,"top":1,"city":"铜陵市","title":"睡觉","category":{"id":1,"name":"保姆"}}]}     * [{"id":1,"name":"失物招领"}]
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 文章id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} city 城市
     * @apiSuccess (返回参数说明) {int} top 是否置顶
     * @apiSuccess (返回参数说明) {Obj} category 圈子类别对象
     * @apiSuccess (返回参数说明) {String} name 类别名称
     * @apiSuccess (返回参数说明) {int} state 圈子状态：1 | 待审核| 2 审核通过
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getCircleListForCMS()
    {
        (new PagingParameter())->goCheck();
        $params = $this->request->param();
        $list = CircleService::getCircleListForCMS($params);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/circle/cms 60-CMS获取指定圈子文章
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定圈子文章
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/cms?id=4
     * @apiParam (请求参数说明) {int} id  文章id
     * @apiSuccessExample {json} 返回样例:
     * {"id":4,"head_img":"http:\/\/repair.com\/static\/imgs\/804054E6-3EB3-0133-C9B6-5F992F52B63B.jpg","content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害。","create_time":"2018-10-09 22:34:18","city":"铜陵市","title":"睡觉","category":{"id":1,"name":"保姆"},"source":{"id":2,"grade":"2"}}
     * @apiSuccess (返回参数说明) {int} id 文章id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} head_img
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {String} city 城市
     * @apiSuccess (返回参数说明) {int} top 是否置顶
     * @apiSuccess (返回参数说明) {Obj} category 圈子类别对象
     * @apiSuccess (返回参数说明) {String} name 类别名称
     * @apiSuccess (返回参数说明) {Obj} source 发布所属对象
     * @apiSuccess (返回参数说明) {String} name grade ： 1| 管理员；2 | 加盟商；2 | 小区管理员
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTheCircle()
    {
        (new CircleValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        return json(CircleT::getCircle($id));

    }

    /**
     * @api {POST} /api/v1/circle/update  61-CMS修改圈子文章
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS修改圈子文章
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "title": "你的睡眠真的好吗？",
     * "head_img": 1,
     * "content": "每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害",
     * "c_id": 1
     * }
     * @apiParam (请求参数说明) {String} title 文章标题
     * @apiParam (请求参数说明) {String} head_img 文章封面图id
     * @apiParam (请求参数说明) {String} content 文章内容
     * @apiParam (请求参数说明) {int} c_id 分类id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function updateCircle()
    {
        (new CircleValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        $circle_id = $params['id'];
        if (isset($params['head_img'])) {
            $params['head_img'] = ImageService::getImageUrl($params['head_img']);
        }
        $id = CircleT::update($params, ['id', $circle_id]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '修改圈子失败',
                'errorCode' => 160007
            ]);

        }
        return json(new  SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/circle/mini/list 62-小程序获取圈子文章列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取圈子文章列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/category/list?page=1&size=20&c_id=1&province="安徽省"&city="铜陵市"&area="铜官区"
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} c_id   圈子类别id
     * @apiParam (请求参数说明) {String} province   省
     * @apiParam (请求参数说明) {String} city   市
     * @apiParam (请求参数说明) {String} area   区
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":8,"head_img":"http:\/\/repair.com\/static\/imgs\/284E6786-D6D7-64D0-30BA-3F359D42A3CA.jpg","create_time":"2018-10-10 01:32:20","title":"睡觉","read_num":0},{"id":7,"head_img":"http:\/\/repair.com\/static\/imgs\/98BBAF17-48E0-317D-7F8D-8A068270094E.jpg","create_time":"2018-10-10 01:23:17","title":"s","read_num":0}]}
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 文章id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} head_img  封面
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {int} read_num 浏览数
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getCircleListForMINI()
    {
        (new CircleValidate())->scene('circle_list_mini')->goCheck();
        $params = $this->request->param();
        $list = CircleService::getCircleListForMINI($params);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/circle/cms 63-小程序获取指定圈子文章
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定圈子文章
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/mini?id=4
     * @apiParam (请求参数说明) {int} id  文章id
     * @apiSuccessExample {json} 返回样例:
     * {"id":7,"content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害。","create_time":"2018-10-10 01:23:17","read_num":0}
     * @apiSuccess (返回参数说明) {int} id 文章id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {int} read_num 阅读量
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCircleForMini()
    {
        (new CircleValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $list = CircleService::getCircleForMini($id);
        return json($list);

    }

    /**
     * @api {POST} /api/v1/circle/comment/save  65-小程序评论圈子文章
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序评论圈子文章
     * @apiExample {post}  请求样例:
     * {
     * "parent_id": 0,
     * "content": "我是一条评论",
     * "c_id": 1
     * }
     * @apiParam (请求参数说明) {int} parent_id 评论上一级id，如果没有则 0
     * @apiParam (请求参数说明) {String} content 评论内容
     * @apiParam (请求参数说明) {int}  c_id 圈子文章id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 评论id
     *
     * @return \think\response\Json
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function saveComment()
    {
        (new CircleValidate())->scene('comment_save')->goCheck();
        $params = $this->request->param();
        $id = CircleService::saveComment($params);

        return json(['id' => $id]);
    }

    /**
     * @api {GET} /api/v1/circle/comment/list 66-小程序获取圈子文章评论列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取圈子文章评论列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/comment/list?id=7&page=1&size=5
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} c_id   圈子文章id
     * @apiSuccessExample {json} 返回样例:
     * {"total":4,"per_page":"5","current_page":1,"last_page":1,"data":[{"id":4,"parent_id":2,"nickName":"朱明良","avatarUrl":"http:\/\/avatarUrl","content":"a2-1","create_time":"2018-10-10 23:13:16","zan":0,"parent_name":"朱明良","parent_url":"http:\/\/avatarUrl","parent_content":"a1-1","state":0},{"id":3,"parent_id":1,"nickName":"朱明良","avatarUrl":"http:\/\/avatarUrl","content":"a1-2","create_time":"2018-10-10 23:13:07","zan":0,"parent_name":"朱明良","parent_url":"http:\/\/avatarUrl","parent_content":"a1","state":0},{"id":2,"parent_id":1,"nickName":"朱明良","avatarUrl":"http:\/\/avatarUrl","content":"a1-1","create_time":"2018-10-10 23:13:00","zan":0,"parent_name":"朱明良","parent_url":"http:\/\/avatarUrl","parent_content":"a1","state":1},{"id":1,"parent_id":0,"nickName":"朱明良","avatarUrl":"http:\/\/avatarUrl","content":"a1","create_time":"2018-10-10 18:53:41","zan":0,"parent_name":null,"parent_url":null,"parent_content":null,"state":1}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 评论id
     * @apiSuccess (返回参数说明) {int} parent_id 评论上级id
     * @apiSuccess (返回参数说明) {String} nickName  用户昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  用户头像
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {int} zan 点赞数
     * @apiSuccess (返回参数说明) {String} parent_name 上级用户名称
     * @apiSuccess (返回参数说明) {String} parent_content 上级用户内容
     * @apiSuccess (返回参数说明) {String} parent_url 上级用户头像
     * @apiSuccess (返回参数说明) {String} state 当前用户是否已经点赞
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getComments()
    {
        (new CircleValidate())->scene('comments')->goCheck();
        $params = $this->request->param();
        $list = CircleService::comments($params);
        return json($list);
    }

    /**
     * @api {POST} /api/v1/circle/comment/zan  67-小程序用户给圈子评论点赞
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户给圈子评论点赞
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 评论id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"state": 1}
     * @apiSuccess (返回参数说明) {int} state 点赞状态：1 | 点赞成功；2 | 已经点赞
     *
     * @return \think\response\Json
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function zan()
    {
        (new CircleValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $res = CircleService::zan($id);
        return json([
            'state' => $res
        ]);
    }


}