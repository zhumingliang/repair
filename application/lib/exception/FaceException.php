<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/14
 * Time: 12:14 PM
 */

namespace app\lib\exception;


class FaceException extends BaseException
{
    public $code = 401;
    public $msg = '百度云人脸识别接口调用失败,图片不合格';
    public $errorCode = 99001;

}