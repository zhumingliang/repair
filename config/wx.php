<?php

return [
    'app_id' => 'wx21b17ce43511ef1a',

    'app_secret' => '7d4f58145e1760f2d586aee87ed8e668',

    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    'qrcode_page' => 'page/index/index?id=%s&grade=%s&client_id=%s',

    'qrcode_url' => 'https://api.weixin.qq.com/wxa/getwxacode?access_token=%s',


];