<?php

namespace app\api\controller\v1;

class Index
{
    public function index()
    {
        echo 'success';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
