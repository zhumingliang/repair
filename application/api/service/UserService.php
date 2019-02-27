<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/21
 * Time: 10:42 PM
 */

namespace app\api\service;


use app\api\model\InviteScoreT;
use app\api\model\ScoreOrderRoleT;
use app\api\model\UserT;
use app\lib\exception\UserInfoException;
use think\Model;

class UserService
{
    public function bindCode($code)
    {
        $info = UserT::where('code', $code)->find();
        if (!$info) {
            throw  new UserInfoException([
                'msg' => '该邀请码无效',
                'errorCode' => 30003

            ]);

        }
        //检测是否已经绑定
        $check = $this->checkBind();
        if ($check['code']) {
            throw  new UserInfoException([
                'msg' => '您已经绑定邀请码，不能重复绑定。',
                'errorCode' => 30005
            ]);
        }
        //进行绑定
        $bind = UserT::update(['parent_id' => $info->id], ['id' => Token::getCurrentUid()]);
        if (!$bind) {
            throw  new UserInfoException([
                'msg' => '绑定邀请码失败',
                'errorCode' => 30004
            ]);
        }
        return $this->saveScore(Token::getCurrentUid(), $info->id);


    }

    private function saveScore($id, $parent_id)
    {
        $info = ScoreOrderRoleT::find();
        $score = 0;
        if ($info) {
            $data = [
                [
                    'u_id' => $id,
                    'parent_id' => $parent_id,
                    'score' => $info->self_register
                ],
                [
                    'u_id' => $parent_id,
                    'parent_id' => 0,
                    'score' => $info->parent_register
                ]
            ];

            (new InviteScoreT())->saveAll($data);
            $score = $info->self_register;
        }
        return [
            'score' => $score
        ];


    }


    public function checkBind()
    {
        $info = UserT::where('id', Token::getCurrentUid())->find();
        if (!$info->parent_id) {
            return [
                'code' => 0,
            ];
        }

        $parent = UserT::where('id', $info->parent_id)->find();
        if (!$parent) {
            return [
                'code' => 0,
            ];
        }

        return [
            'code' => $parent->code ? $parent->code : 0,
        ];

    }


}