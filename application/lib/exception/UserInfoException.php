<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/21
 * Time: 下午9:33
 */

namespace app\lib\exception;


class UserInfoException extends BaseException
{

    public $code = 401;
    public $msg = '微信用户信息写入数据库失败';
    public $errorCode = 30001;
}