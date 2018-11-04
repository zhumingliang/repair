<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 11:28 PM
 */

namespace app\api\service;


use app\api\model\ServiceCommentV;

class CommentService
{
    /**
     * 获取指定服务的评论
     * @param $id
     * @param $page
     * @param $size
     * @return \think\Paginator
     */
    public static function getCommentForService($id, $page, $size)
    {
        return ServiceCommentV::getListForService($id, $page, $size);


    }

}