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
Route::post('api/:version/user/info', 'api/:version.User/userInfo');
Route::post('api/:version/user/update', 'api/:version.User/infoUpdate');
Route::get('api/:version/user/list', 'api/:version.User/getUsers');

Route::get('api/:version/behaviors', 'api/:version.Behavior/getList');
Route::get('api/:version/behavior/handel', 'api/:version.Behavior/handel');

Route::rule('api/:version/image/save', 'api/:version.Image/save');
Route::rule('api/:version/image/upload', 'api/:version.Image/upload');
Route::rule('api/:version/image/search', 'api/:version.Image/search');

Route::rule('api/:version/demand/save', 'api/:version.Demand/save');
Route::rule('api/:version/demand/handel', 'api/:version.Demand/handel');
Route::get('api/:version/demand/list', 'api/:version.Demand/getList');
Route::get('api/:version/demand', 'api/:version.Demand/getTheDemand');

Route::post('api/:version/shop/apply', 'api/:version.Shop/ShopApply');
Route::post('api/:version/shop/update', 'api/:version.Shop/updateShop');
Route::get('api/:version/shop/handel', 'api/:version.Shop/handel');
Route::get('api/:version/shop/info', 'api/:version.Shop/shopInfo');
Route::get('api/:version/shop/info/cms', 'api/:version.Shop/shopInfoForCMS');
Route::get('api/:version/shop/info/normal', 'api/:version.Shop/getShopInfoForNormal');
Route::get('api/:version/shop/info/edit', 'api/:version.Shop/shopInfoForEdit');
Route::post('api/:version/shop/service/save', 'api/:version.Shop/addService');
Route::post('api/:version/service/booking', 'api/:version.Shop/bookingService');
Route::get('api/:version/service/mini/list', 'api/:version.Shop/getServiceListForMini');
Route::get('api/:version/shop/staff', 'api/:version.Shop/shopStaff');
Route::get('api/:version/shop/service/list', 'api/:version.Shop/getServiceList');
Route::post('api/:version/shop/staff/examine', 'api/:version.Shop/examineStaff');
Route::post('api/:version/shop/staff/delete', 'api/:version.Shop/deleteStaff');
Route::post('api/:version/shop/service/delete', 'api/:version.Shop/deleteService');
Route::get('api/:version/shop/service/normal/list', 'api/:version.Shop/getServiceListForNormal');
Route::get('api/:version/shops/list/cms', 'api/:version.Shop/ShopsForCMS');
Route::get('api/:version/shop/info/cms', 'api/:version.Shop/shopInfoForCMS');
Route::get('api/:version/shop/frozen', 'api/:version.Shop/shopFrozen');
Route::get('api/:version/shop/service', 'api/:version.Shop/getTheService');


Route::post('api/:version/message/save', 'api/:version.Message/save');
Route::post('api/:version/message/handel', 'api/:version.Message/handel');
Route::get('api/:version/message/list', 'api/:version.Message/getMessages');

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
Route::get('api/:version/guid', 'api/:version.Guid/getTheGuid');
Route::get('api/:version/guid/init', 'api/:version.Guid/guidInit');
Route::get('api/:version/guid/init/handel', 'api/:version.Guid/initHandel');


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
Route::get('api/:version/service/index', 'api/:version.ServicesExtend/getServiceIndex');
Route::get('api/:version/services', 'api/:version.ServicesExtend/getServiceForCMS');


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
Route::post('api/:version/circle/comment/zan', 'api/:version.Circle/zan');

Route::post('api/:version/order/taking', 'api/:version.Order/orderTaking');
Route::post('api/:version/order/phone/confirm', 'api/:version.Order/phone');
Route::post('api/:version/order/price/update', 'api/:version.Order/updatePrice');
Route::get('api/:version/order', 'api/:version.Order/getOrderInfo');
Route::get('api/:version/order/demand/list', 'api/:version.Order/getDemandList');
Route::get('api/:version/order/service/list', 'api/:version.Order/getServiceList');
Route::post('api/:version/order/comment', 'api/:version.Order/comment');
Route::get('api/:version/order/comments', 'api/:version.Order/getCommentsForShop');
Route::post('api/:version/order/confirm', 'api/:version.Order/confirm');
Route::post('api/:version/order/shop/confirm', 'api/:version.Order/shopConfirmOrder');
Route::post('api/:version/order/service/begin', 'api/:version.Order/serviceBegin');
Route::get('api/:version/index/search', 'api/:version.Order/indexSearch');
Route::post('api/:version/order/pay/check', 'api/:version.Order/checkPay');
Route::post('api/:version/order/phone/check', 'api/:version.Order/checkPhone');
Route::post('api/:version/order/service/handel', 'api/:version.Order/serviceHandel');


Route::get('api/:version/pay/getPreOrder', 'api/:version.Pay/getPreOrder');
Route::rule('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');

Route::get('api/:version/bond/check', 'api/:version.Shop/checkBalanceForBond');
Route::post('api/:version/bond/save', 'api/:version.Bond/save');
Route::post('api/:version/bond/operation', 'api/:version.Bond/operation');

Route::get('api/:version/withdraw/balance', 'api/:version.Withdraw/getBalance');
Route::get('api/:version/withdraw/bond/check', 'api/:version.Withdraw/checkBond');
Route::get('api/:version/withdraw/check', 'api/:version.Withdraw/checkWithdraw');
Route::post('api/:version/withdraw/apply', 'api/:version.Withdraw/apply');
Route::get('api/:version/withdraws', 'api/:version.Withdraw/getWithdrawList');
Route::get('api/:version/payments', 'api/:version.Withdraw/getPayments');

Route::get('api/:version/center/info', 'api/:version.PersonalCenter/getInfo');
Route::get('api/:version/center/msgs', 'api/:version.PersonalCenter/getMsgs');

Route::get('api/:version/units/mini', 'api/:version.Unit/getUnitsForMini');

Route::post('api/:version/system/file/save', 'api/:version.System/saveFile');
Route::post('api/:version/system/file/update', 'api/:version.System/updateFile');
Route::get('api/:version/system/file', 'api/:version.System/file');
Route::get('api/:version/system/demand', 'api/:version.System/getDemand');
Route::post('api/:version/system/demand/update', 'api/:version.System/updateDemand');
Route::post('api/:version/system/invoice/save', 'api/:version.System/saveInvoice');
Route::post('api/:version/system/invoice/update', 'api/:version.System/updateInvoice');
Route::get('api/:version/system/invoice', 'api/:version.System/invoice');
Route::post('api/:version/system/tip/save', 'api/:version.System/saveTip');
Route::post('api/:version/system/tip/update', 'api/:version.System/updateTip');
Route::get('api/:version/system/tip', 'api/:version.System/tip');
Route::post('api/:version/system/time/save', 'api/:version.System/saveOrderTime');
Route::post('api/:version/system/time/update', 'api/:version.System/updateOrderTime');
Route::get('api/:version/system/time', 'api/:version.System/orderTime');
Route::post('api/:version/system/phone/save', 'api/:version.System/savePhone');
Route::post('api/:version/system/phone/update', 'api/:version.System/updatePhone');
Route::get('api/:version/system/phone', 'api/:version.System/phone');
Route::post('api/:version/system/join/save', 'api/:version.JoinCommission/save');
Route::post('api/:version/system/join/handel', 'api/:version.JoinCommission/handel');
Route::get('api/:version/system/join/list', 'api/:version.JoinCommission/getList');


Route::get('api/:version/report/export/city', 'api/:version.OrderReport/exportWithCity');
Route::get('api/:version/report/export', 'api/:version.OrderReport/exportWithoutCity');
Route::get('api/:version/report/demand/admin', 'api/:version.OrderReport/getDemandReportForAdmin');
Route::get('api/:version/report/service/admin', 'api/:version.OrderReport/getServiceReportForAdmin');
Route::get('api/:version/report/order/join', 'api/:version.OrderReport/getOrderReportForJoin');

Route::post('api/:version/admin/village/save', 'api/:version.Admin/addVillage');
Route::post('api/:version/admin/join/save', 'api/:version.Admin/addJoin');
Route::post('api/:version/admin/handel', 'api/:version.Admin/handel');
Route::get('api/:version/admin/villages', 'api/:version.Admin/getVillageList');
Route::get('api/:version/admin/joins', 'api/:version.Admin/getJoinList');
