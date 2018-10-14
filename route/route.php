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

Route::rule('/', 'index');

Route::get('api/:version/index', 'api/:version.Index/index');

Route::get('api/:version/token/admin', 'api/:version.Token/getAdminToken');
Route::get('api/:version/token/user', 'api/:version.Token/getUserToken');
//Route::get('api/:version/token/userInfo', 'api/:version.Token/userInfo');
Route::post('api/:version/user/update', 'api/:version.User/infoUpdate');

Route::rule('api/:version/image/save', 'api/:version.Image/save');
Route::rule('api/:version/image/upload', 'api/:version.Image/upload');

Route::rule('api/:version/demand/save', 'api/:version.Demand/save');
Route::rule('api/:version/demand/handel', 'api/:version.Demand/handel');
Route::get('api/:version/demand/list', 'api/:version.Demand/getList');
Route::get('api/:version/demand', 'api/:version.Demand/getTheDemand');

Route::post('api/:version/shop/apply', 'api/:version.Shop/ShopApply');
Route::get('api/:version/shop/handel', 'api/:version.Shop/handel');
Route::get('api/:version/shop/info', 'api/:version.Shop/shopInfo');
Route::get('api/:version/shop/info/edit', 'api/:version.Shop/shopInfoForEdit');
Route::post('api/:version/shop/service/save', 'api/:version.Shop/addService');
Route::post('api/:version/bond/check', 'api/:version.Shop/checkBalanceForBond');
Route::post('api/:version/service/booking', 'api/:version.Shop/bookingService');
Route::get('api/:version/service/mini/list', 'api/:version.Shop/getServiceListForMini');
Route::get('api/:version/shop/staff', 'api/:version.Shop/shopStaff');
Route::post('api/:version/shop/staff/examine', 'api/:version.Shop/examineStaff');
Route::post('api/:version/shop/staff/delete', 'api/:version.Shop/deleteStaff');

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
Route::get('api/:version/banner/mini/list', 'api/:version.Banner/getListForMini');
Route::get('api/:version/banner/cms/list', 'api/:version.Banner/getListForCms');
Route::get('api/:version/banner', 'api/:version.Banner/getTheBanner');


Route::post('api/:version/guid/save', 'api/:version.Guid/save');
Route::post('api/:version/guid/handel', 'api/:version.Guid/handel');
Route::post('api/:version/guid/update', 'api/:version.Guid/update');
Route::get('api/:version/guid/list', 'api/:version.Guid/getList');
Route::get('api/:version/guid', 'api/:version.Banner/getTheGuid');


Route::post('api/:version/category/save', 'api/:version.Category/save');
Route::post('api/:version/category/handel', 'api/:version.Category/handel');
Route::post('api/:version/category/update', 'api/:version.Category/update');
Route::get('api/:version/category/mini/list', 'api/:version.Category/getListForMini');
Route::get('api/:version/category/cms/list', 'api/:version.Category/getListForCms');
Route::get('api/:version/category', 'api/:version.Banner/getTheCategory');

Route::get('api/:version/extend/cms/list', 'api/:version.ServicesExtend/getListForCMS');
Route::get('api/:version/extend/handel', 'api/:version.ServicesExtend/handel');
Route::get('api/:version/extend/service', 'api/:version.ServicesExtend/getTheService');
Route::get('api/:version/extend/mini/service', 'api/:version.ServicesExtend/getServiceForMini');
Route::get('api/:version/extend/house', 'api/:version.ServicesExtend/getHoursList');
Route::get('api/:version/extend/repair', 'api/:version.ServicesExtend/getRepairList');


Route::post('api/:version/city/discount/save', 'api/:version.CityDiscount/save');
Route::post('api/:version/city/discount/handel', 'api/:version.CityDiscount/handel');
Route::post('api/:version/city/discount/update', 'api/:version.CityDiscount/update');
Route::get('api/:version/city/discount/list', 'api/:version.CityDiscount/getList');


Route::get('api/:version/comment/service', 'api/:version.Comment/getCommentForService');


Route::post('api/:version/rank/save', 'api/:version.Rank/save');
Route::get('api/:version/rank/list', 'api/:version.Rank/getRank');


Route::post('api/:version/circle/category/save', 'api/:version.Circle/saveCategory');
Route::post('api/:version/circle/category/handel', 'api/:version.Circle/categoryHandel');
Route::get('api/:version/circle/cms/category/list', 'api/:version.Circle/getCategoryListForCms');
Route::get('api/:version/circle/mini/category/list', 'api/:version.Circle/getCategoryListForMini');
Route::post('api/:version/circle/pass/set', 'api/:version.Circle/circlePassSet');
Route::get('api/:version/circle/pass/get', 'api/:version.Circle/getCirclePassSet');
Route::post('api/:version/circle/save', 'api/:version.Circle/saveCircle');
Route::post('api/:version/circle/update', 'api/:version.Circle/updateCircle');
Route::post('api/:version/circle/handel', 'api/:version.Circle/handel');
Route::post('api/:version/circle/top/handel', 'api/:version.Circle/topHandel');
Route::get('api/:version/circle/cms/list', 'api/:version.Circle/getCircleListForCMS');
Route::get('api/:version/circle/mini/list', 'api/:version.Circle/getCircleListForMINI');
Route::get('api/:version/circle/cms', 'api/:version.Circle/getTheCircle');
Route::get('api/:version/circle/mini', 'api/:version.Circle/getCircleForMini');
Route::post('api/:version/circle/comment/save', 'api/:version.Circle/saveComment');
Route::get('api/:version/circle/comment/list', 'api/:version.Circle/getComments');
