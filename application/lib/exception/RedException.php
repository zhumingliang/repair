<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/28
 * Time: 上午12:17
 */

namespace app\lib\exception;


class RedException extends BaseException
{

    public $code = 401;
    public $msg = '新增红包失败';
    public $errorCode = 90001;

}