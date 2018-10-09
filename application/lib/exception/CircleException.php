<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/21
 * Time: 下午9:33
 */

namespace app\lib\exception;


class CircleException extends BaseException
{

    public $code = 401;
    public $msg = '圈子类别新增失败';
    public $errorCode = 160001;
}