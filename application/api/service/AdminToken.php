<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/5/19
 * Time: 18:27
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\lib\enum\UserEnum;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;

class AdminToken extends Token
{
    protected $phone;
    protected $pwd;


    function __construct($phone, $pwd)
    {
        $this->phone = $phone;
        $this->pwd = $pwd;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function get()
    {
        try {

            $admin = AdminT::where('phone', '=', $this->phone)
                ->with('adminJoin')
                ->find();

            if (is_null($admin)) {
                throw new TokenException([
                    'code' => 401,
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


    /**
     * @param $key
     * @param $cachedValue
     * @return mixed
     * @throws TokenException
     */
    private function saveToCache($key, $cachedValue)
    {
        $key = empty($key) ? self::generateToken() : $key;
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $request = Cache::remember($key, $value, $expire_in);


        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 20002
            ]);
        }

        $cachedValue['token'] = $key;
        unset($cachedValue['phone']);
        unset($cachedValue['u_id']);
        return $cachedValue;
    }

    private function prepareCachedValue($admin)
    {

        $cachedValue = [
            'u_id' => $admin->id,
            'phone' => $admin->phone,
            'username' => $admin->username,
            'grade' => $admin->grade,
            'parent_id' => $admin->parent_id,
        ];

        $cachedValue['province'] = '';
        $cachedValue['city'] = '';
        $cachedValue['area'] = '';
        $admin_join = array();

        if ($admin->grade == UserEnum::USER_GRADE_ADMIN || $admin->grade == UserEnum::USER_GRADE_JOIN) {
            if (isset($admin->adminJoin)) {
                $admin_join = $admin->adminJoin;
            }
        } else {
            $parent_id = $admin->parent_id;
            if ($parent_id) {
                $parent = $this->getParent($parent_id);
                if (isset($parent->adminJoin)) {
                    $admin_join = $parent->adminJoin;
                }
            }

        }
        if ($admin_join) {
            $cachedValue['province'] = $admin_join->province;
            $cachedValue['city'] = $admin_join->city;
            $cachedValue['area'] = $admin_join->area;
        }

        return $cachedValue;
    }

    private function getParent($id)
    {
        $admin = AdminT::where('id', $id)
            ->with('adminJoin')
            ->find();
        return $admin;

    }


}