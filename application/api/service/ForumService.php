<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-30
 * Time: 11:12
 */

namespace app\api\service;


use app\api\model\ForumCommentListV;
use app\api\model\ForumCommentT;
use app\api\model\ForumCommentV;
use app\api\model\ForumImgT;
use app\api\model\ForumListV;
use app\api\model\ForumT;
use app\api\model\ForumV;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use function Composer\Autoload\includeFile;
use think\Db;
use think\Exception;
use think\Model;

class ForumService
{

    private $list_all = 2;
    private $list_self = 1;

    public function save($params)
    {
        Db::startTrans();
        try {
            $params['u_id'] = Token::getCurrentUid();
            $params['state'] = CommonEnum::STATE_IS_OK;
            $forum = ForumT::create($params);
            if (!$forum) {
                Db::rollback();
                throw new OperationException(
                    [
                        'code' => 401,
                        'msg' => '新增帖子失败',
                        'errorCode' => 160004
                    ]);
            }
            if (key_exists('imgs', $params) && strlen($params['imgs'])) {
                $data = $this->preImage($params['imgs'], $forum->id);
                $res = (new ForumImgT())->saveAll($data);
                if (!$res) {
                    Db::rollback();
                    throw new OperationException(
                        [
                            'code' => 401,
                            'msg' => '新增帖子图片失败',
                            'errorCode' => 160004
                        ]);
                }
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }


    public function update($params)
    {
        Db::startTrans();
        try {
            $forum = ForumT::update($params, ['id', $params['id']]);
            if (!$forum) {
                Db::rollback();
                throw new OperationException(
                    [
                        'code' => 401,
                        'msg' => '更新帖子失败',
                        'errorCode' => 160004
                    ]);
            }
            if (key_exists('imgs', $params) && strlen($params['imgs'])) {
                $data = $this->preImage($params['imgs'], $params['id']);
                $res = (new ForumImgT())->saveAll($data);
                if (!$res) {
                    Db::rollback();
                    throw new OperationException(
                        [
                            'code' => 401,
                            'msg' => '更新帖子图片失败',
                            'errorCode' => 160004
                        ]);
                }
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 处理图片
     * @param $img
     * @param $f_id
     * @return mixed
     */
    private
    function preImage($img, $f_id)
    {
        $return_arr = array();
        $img_arr = explode(',', $img);
        foreach ($img_arr as $k => $v) {
            $list = [
                'f_id' => $f_id,
                'img_id' => $v,
                'state' => CommonEnum::STATE_IS_OK,

            ];

            array_push($return_arr, $list);
        }
        return $return_arr;

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
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['u_id'] = Token::getCurrentUid();
        $comments = ForumCommentT::create($params);
        if (!$comments->id) {
            throw new OperationException(['code' => 401,
                'msg' => '新增评论失败',
                'errorCode' => 160008
            ]);

        }
        return $comments->id;

    }


    public function getForumListForCMS($type, $page, $size, $key)
    {
        $list = ForumV::getListForCms($type, $page, $size, $key);
        return $list;

    }

    public function getForumListForMINI($type, $page, $size)
    {
        $list = array();
        if ($type == $this->list_self) {
            $list = $this->getListForSelf($page, $size);
        } elseif ($type == $this->list_all) {
            $list = $this->getListForAll($page, $size);
            $list['data'] = $this->prefixComment($list['data']);
        }

        return $list;
    }


    private function getListForSelf($page, $size)
    {
        $u_id = Token::getCurrentUid();
        $list = ForumListV::getListForSelf($u_id, $page, $size);
        return $list;

    }

    private function getListForAll($page, $size)
    {
        $list = ForumListV::getListForAll($page, $size);
        return $list;
    }

    private function prefixComment($data)
    {
        if (!count($data)) {
            return $data;
        }
        foreach ($data as $k => $v) {
            if ($v['c_count'] == 0) {
                $data[$k]['comment'] = array();
                continue;
            }
            $data[$k]['comment'] = $this->getCommentsForMINI($v['id']);

        }

        return $data;

    }

    public function getCommentsForMINI($f_id, $page = 1, $size = 5)
    {
        $comment = ForumCommentV::getComment($f_id, $page, $size);
        return $comment;

    }

    public function getForumForMINI($id)
    {
        $info = ForumT::getForumForCMS($id);
        $info['comment'] = $this->getCommentsForMINI($id);
        return $info;
    }

    public function getCommentListForCMS($day, $type, $page, $size, $key)
    {
        $list = ForumCommentListV::getList($day, $type, $page, $size, $key);
        return $list;

    }


}