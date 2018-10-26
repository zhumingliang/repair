<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/26
 * Time: 11:50 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SystemMsgT;
use app\lib\exception\FileException;
use app\lib\exception\SuccessMessage;

class SystemFile extends BaseController
{
    public function save($type, $content)
    {
        $msg = SystemMsgT::create([
            'type' => $type,
            'content' => $content
        ]);
        if (!$msg->id) {
            throw new  FileException();
        }
        return json(new  SuccessMessage());
    }


    public function file($type)
    {


    }

}