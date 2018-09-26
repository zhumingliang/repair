<?php

return [
    'app_id' => 'wxd11ca80b97562519',

    'app_secret' => '6a2fcc652491dee6c825e8a5cd178ee2',

    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    'qrcode_page' => 'page/index/index?id=%s&grade=%s&client_id=%s',

    'qrcode_url' => 'https://api.weixin.qq.com/wxa/getwxacode?access_token=%s',


];