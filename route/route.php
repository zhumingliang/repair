<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');
Route::get('api/:version/index', 'api/:version.Index/index');

//Route::get('api/:version/token/userInfo', 'api/:version.Token/userInfo');
//Route::get('api/:version/token/userInfo', 'api/:version.Token/userInfo');
Route::post('api/:version/user/update', 'api/:version.User/infoUpdate');

Route::post('api/:version/image/save', 'api/:version.Image/save');

Route::post('api/:version/demand/save', 'api/:version.Demand/save');

Route::post('api/:version/shop/apply', 'api/:version.Shop/ShopApply');
Route::get('api/:version/shop/handel', 'api/:version.Shop/handel');
Route::post('api/:version/shop/service/save', 'api/:version.Shop/addService');

Route::post('api/:version/message/save', 'api/:version.Message/save');

Route::post('api/:version/collection/save', 'api/:version.Collection/save');
Route::post('api/:version/collection/handel', 'api/:version.Collection/handel');

