<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/4/16
 * Time: 上午9:56
 */

namespace app\lib\enum;


class UserEnum
{

    //管理员
    const USER_GRADE_ADMIN = 1;


    //录出员
    const USER_GRADE_JOIN = 2;

    //账号正常
    const USER_STATE_OK = 1;

    //账号停用
    const USER_STATE_STOP = 2;


}