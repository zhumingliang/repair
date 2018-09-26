<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:34
 */

namespace app\lib\exception;


class ImageException extends BaseException
{
    public $code = 401;
    public $msg = '新增图片失败';
    public $errorCode = 40001;

}