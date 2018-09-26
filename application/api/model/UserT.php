<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午3:16
 */

namespace app\api\model;


use think\Model;

class UserT extends Model
{
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getGenderAttr($value)
    {
        if ($value == 1) {
            return '男';
        }
        if ($value == 0) {
            return '未知';
        }
        if ($value == 2) {
            return '女';
        }
    }

    /**
     * 根据openid获取用户数据
     * @param $openId
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getByOpenID($openId)
    {
        $user = self::where('openId', '=', $openId)
            ->find();
        return $user;
    }


}