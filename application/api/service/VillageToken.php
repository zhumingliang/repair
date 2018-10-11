<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/11
 * Time: 11:12 PM
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\lib\enum\UserEnum;
use app\lib\exception\TokenException;
use think\Exception;

class VillageToken
{
    protected $phone;
    protected $pwd;


    function __construct($phone, $pwd)
    {
        $this->phone = $phone;
        $this->pwd = $pwd;
    }


    public function get()
    {
        try {

            $admin = AdminT::where('phone', '=', $this->phone)
                ->where('grade', UserEnum::USER_GRADE_VILLAGE)
                ->find();

            if (is_null($admin)) {
                throw new TokenException([
                    'code' => 404,
                    'msg' => '用户不存在',
                    'errorCode' => 30000
                ]);
            }

            if (sha1($this->pwd) != $admin->pwd) {
                throw new TokenException([
                    'code' => 401,
                    'msg' => '密码不正确',
                    'errorCode' => 30002
                ]);
            }

            if ($admin->state > 1) {

                throw new TokenException([
                    'code' => 401,
                    'msg' => '该账号已暂停使用，请联系管理员',
                    'errorCode' => 30004
                ]);
            }

            /**
             * 获取缓存参数
             */
            $cachedValue = $this->prepareCachedValue($admin);
            /**
             * 缓存数据
             */
            $token = $this->saveToCache('', $cachedValue);
            return $token;

        } catch (Exception $e) {
            throw $e;
        }

    }


    private function getJoin($parent_id)
    {
        $admin = AdminT::where('phone', '=', $this->phone)
            ->with('adminJoin')
            ->find();

    }

}