<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:34
 */

namespace app\lib\exception;


class MessageException extends BaseException
{
    public $code = 401;
    public $msg = '新增留言失败';
    public $errorCode = 70001;

}