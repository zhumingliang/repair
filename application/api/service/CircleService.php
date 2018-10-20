<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:29 PM
 */

namespace app\api\service;


use app\api\model\CircleCategoryT;
use app\api\model\CircleCommentT;
use app\api\model\CircleCommentV;
use app\api\model\CircleExamineT;
use app\api\model\CircleT;
use app\api\model\CommentZanT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\CircleException;

class CircleService
{
    const CIRCLE_NEED_EXAMINE = 2;
    const CIRCLE_NOT_TOP = 1;
    const CIRCLE_TOP = 2;

    /**
     * CMS 获取圈子类别列表（管理员-圈子分类列表/加盟商-新增圈子时获取分类列表）
     * @param $params
     * @return array|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategoryListForCms($params)
    {
        $grade = Token::getCurrentTokenVar('grade');
        $list = array();
        if ($grade == UserEnum::USER_GRADE_ADMIN) {
            $page = $params['page'];
            $size = $params['size'];
            $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
                ->hidden(['state', 'update_time'])
                ->paginate($size, false, ['page' => $page]);

        } else if ($grade == UserEnum::USER_GRADE_JOIN) {
            $province = Token::getCurrentTokenVar('province');
            $city = Token::getCurrentTokenVar('city');
            $area = Token::getCurrentTokenVar('area');
            $sql = preJoinSql($province, $city, $area);
            $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
                ->whereRaw($sql)
                ->hidden(['state', 'create_time', 'update_time', 'province', 'city', 'area'])
                ->select();
        }


        return $list;
    }

    /**
     * 小程序获取指定区域内圈子列表
     * @param $params
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategoryListForMini($params)
    {
        $province = $params['province'];
        $city = $params['city'];
        $area = $params['area'];
        $sql = preJoinSql($province, $city, $area);
        $list = CircleCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->hidden(['state', 'create_time', 'update_time', 'province', 'city', 'area'])
            ->select();
        return $list;

    }


    /**
     * 保存圈子
     * @param $params
     * @throws CircleException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function saveCircle($params)
    {
        $u_id = Token::getCurrentUid();
        $grade = Token::getCurrentTokenVar('grade');
        $params['u_id'] = $u_id;
        $params['state'] = self::checkCircleDefault() == self::CIRCLE_NEED_EXAMINE ? CommonEnum::READY : CommonEnum::PASS;
        $params['top'] = self::CIRCLE_NOT_TOP;
        $params['read_num'] = 0;
        $params['province'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('province');
        $params['city'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('city');
        $params['area'] = $grade == UserEnum::USER_GRADE_ADMIN ? "全部" : Token::getCurrentTokenVar('area');
        $params['parent_id'] = Token::getCurrentTokenVar('grade') == UserEnum::USER_GRADE_JOIN ? Token::getCurrentUid() : Token::getCurrentTokenVar('parent_id');

        if (isset($params['head_img'])) {
            $params['head_img'] = ImageService::getImageUrl($params['head_img']);
        }

        $id = CircleT::create($params);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '新增圈子失败',
                'errorCode' => 160004
            ]);

        }
    }

    /**
     * 查看圈子是否审核默认设置
     * @return array|int|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function checkCircleDefault()
    {
        $default = (new CircleExamineT())->field('default')->find();

        return $default ? $default->default : 2;
    }


    public static function getCircleListForCMS($params)
    {
        $list = CircleT::getListForCms($params['page'], $params['size'], $params['state']);
        return $list;

    }


    public static function getCircleListForMINI($params)
    {
        $list = CircleT::getListForMINI($params['page'], $params['size'], $params['province'], $params['city'], $params['area'], $params['c_id']);
        return $list;

    }

    /**
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCircleForMini($id)
    {
        CircleT::where('id', $id)
            ->inc('read_num', 1)->update();;

        $circle = CircleT::getCircleForMINI($id);
        return $circle;

    }

    public static function comments($params)
    {

        $list = CircleCommentV::getList($params['page'], $params['size'], $params['id']);
        $data = $list['data'];
        if (count($data)) {
            foreach ($data as $k => $v) {
                if (empty($data[$k]['zans'])) {
                    $data[$k]['state'] = 0;
                } else {
                    $data[$k]['state'] = 1;

                }
                unset($data[$k]['zans']);
            }

            $list['data'] = $data;
        }

        return $list;

        /*  //获取评论信息
          $list = CircleCommentT::getList($params['page'], $params['size'], $params['id']);
          $list['data'] = self::preComments($list['data']);
          return $list;*/

    }


    private static function preComments($list)
    {
        if (empty($list)) {
            return array();
        }

        foreach ($list as $k => $v) {
            $list[$k]['children'] = self::getCommentList($v['id']);
        }

        return $list;

    }

    protected static function getCommentList($parent_id = 0, &$result = array())
    {
        $arr = CircleCommentT::where('parent_id', '=', $parent_id)
            ->field('id,parent_id,nickName,avatarUrl,content,create_time')
            ->order("create_time desc")->select();
        if (empty($arr)) {
            return array();
        }
        foreach ($arr as $cm) {
            $thisArr =& $result[];
            $cm["children"] = self::getCommentList($cm["id"], $thisArr);
            $thisArr = $cm;
        }
        return $result;
    }

    /**
     * @param $params
     * @return mixed
     * @throws CircleException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function saveComment($params)
    {
        $params['openid'] = Token::getCurrentTokenVar('openId');
        $params['nickName'] = Token::getCurrentTokenVar('nickName');
        $params['avatarUrl'] = Token::getCurrentTokenVar('avatarUrl');
        $params['zan'] = 0;
        $comments = CircleCommentT::create($params);
        if (!$comments->id) {
            throw new CircleException(['code' => 401,
                'msg' => '新增圈子评论失败',
                'errorCode' => 160008
            ]);

        }
        return $comments->id;

    }

    /**
     * @param $id
     * @return int
     * @throws CircleException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function zan($id)
    {
        if (!self::checkZan($id)) {
            $zan_id = CommentZanT::create(['u_id' => Token::getCurrentUid(), 'c_id' => $id]);
            if (!$zan_id) {
                throw new CircleException(['code' => 401,
                    'msg' => '添加用户点赞记录失败',
                    'errorCode' => 160009
                ]);

            }
            $up_id = CircleCommentT::where('id', $id)
                ->inc('zan')->update();
            if (!$up_id) {
                throw new CircleException(['code' => 401,
                    'msg' => '用户点赞失败',
                    'errorCode' => 160010
                ]);
            }

            return 1;
        }

        return 2;


    }

    /**
     * 检测用是否已经评论
     * @param $id
     * @return float|string
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private static function checkZan($id)
    {
        $count = CommentZanT::where('c_id', $id)
            ->where('u_id', Token::getCurrentUid())
            ->count();
        return $count;

    }


}