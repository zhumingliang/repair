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

Route::rule('/', 'index'); // 首页访问路由

Route::get('api/:version/index', 'api/:version.Index/index');

//Route::get('api/:version/token/userInfo', 'api/:version.Token/userInfo');
//Route::get('api/:version/token/userInfo', 'api/:version.Token/userInfo');
Route::post('api/:version/user/update', 'api/:version.User/infoUpdate');

Route::post('api/:version/image/save', 'api/:version.Image/save');

Route::post('api/:version/demand/save', 'api/:version.Demand/save');

Route::post('api/:version/shop/apply', 'api/:version.Shop/ShopApply');
Route::get('api/:version/shop/handel', 'api/:version.Shop/handel');
Route::post('api/:version/shop/service/save', 'api/:version.Shop/addService');
Route::post('api/:version/bond/check', 'api/:version.Shop/checkBalanceForBond');

Route::post('api/:version/message/save', 'api/:version.Message/save');

Route::post('api/:version/collection/save', 'api/:version.Collection/save');
Route::post('api/:version/collection/handel', 'api/:version.Collection/handel');
Route::get('api/:version/collection/list', 'api/:version.Collection/getList');


Route::get('api/:version/red/list', 'api/:version.Red/getList');
Route::get('api/:version/red/strategy', 'api/:version.Red/getStrategyList');
Route::post('api/:version/strategy/save', 'api/:version.Red/saveStrategy');
Route::post('api/:version/strategy/update', 'api/:version.Red/updateStrategy');
Route::post('api/:version/strategy/delete', 'api/:version.Red/deleteStrategy');


Route::post('api/:version/banner/save', 'api/:version.Banner/save');
Route::post('api/:version/banner/handel', 'api/:version.Banner/handel');
Route::post('api/:version/banner/update', 'api/:version.Banner/update');

