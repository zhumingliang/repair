define({ "api": [  {    "type": "GET",    "url": "/api/v1/shop/handel",    "title": "7-商铺申请审核",    "group": "CMS",    "version": "1.0.1",    "description": "<p>管理员审核商铺申请：同意或者拒绝</p>",    "examples": [      {        "title": "请求样例:",        "content": "http://mengant.cn/api/v1/shop/handel?id=1&state=2",        "type": "get"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "id",            "description": "<p>申请id</p>"          },          {            "group": "请求参数说明",            "type": "int",            "optional": false,            "field": "state",            "description": "<p>申请操作：2 | 同意；3 | 拒绝</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误码： 0表示操作成功无错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>信息描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Shop.php",    "groupTitle": "CMS",    "name": "GetApiV1ShopHandel"  },  {    "type": "GET",    "url": "/api/v1/token/loginOut",    "title": "4-CMS退出登陆",    "group": "CMS",    "version": "1.0.1",    "description": "<p>CMS退出当前账号登陆。</p>",    "examples": [      {        "title": "请求样例:",        "content": "http://test.mengant.cn/api/v1/token/loginOut",        "type": "get"      }    ],    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误码： 0表示操作成功无错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>信息描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Token.php",    "groupTitle": "CMS",    "name": "GetApiV1TokenLoginout"  },  {    "type": "GET",    "url": "/api/v1/token/user",    "title": "1-小程序端获取登录token",    "group": "MINI",    "version": "1.0.1",    "description": "<p>微信用户登录获取token。 前端判断返回数据，如果用户信息缓存了并且grade=2、3时 跳转绑定手机页面； 否则依据type值，进行下一步操作</p>",    "examples": [      {        "title": "请求样例:",        "content": "http://mengant.cn/api/v1/token/user?code=mdksk",        "type": "get"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "code",            "description": "<p>小程序code</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"token\":\"f4ad56e55cad93833180186f22586a08\",\"type\":1}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "Sting",            "optional": false,            "field": "token",            "description": "<p>口令令牌，每次请求接口需要传入，有效期 2 hours</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "type",            "description": "<p>数据库是否缓存小程序用户信息 type=1时，表示已缓存 type=2 表示没有缓存数据，需要请求userInfo接口</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Token.php",    "groupTitle": "MINI",    "name": "GetApiV1TokenUser"  },  {    "type": "GET",    "url": "/api/v1/user/update",    "title": "8-用户信息编辑",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户编辑自己的信息,修改了的字段才传入。</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"avatarUrl\": adadsasdvadvadf,\n\"nickName\": \"朱明良\",\n\"phone\": \"18956225230\",\n\"address\": 广州市天河区,\n}",        "type": "POST"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "avatarUrl",            "description": "<p>用户头像 base64</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "nickName",            "description": "<p>用户昵称</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "phone",            "description": "<p>联系方式</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>所在地</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/User.php",    "groupTitle": "MINI",    "name": "GetApiV1UserUpdate"  },  {    "type": "POST",    "url": "/api/v1/collection/handel",    "title": "11-用户取消收藏服务/店铺",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户取消收藏服务/店铺</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"id\": 1,\n\"type\":1\n}",        "type": "POST"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "id",            "description": "<p>收藏 id</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "type",            "description": "<p>收藏类别：1 服务；2| 店铺</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Collection.php",    "groupTitle": "MINI",    "name": "PostApiV1CollectionHandel"  },  {    "type": "POST",    "url": "/api/v1/collection/save",    "title": "10-用户收藏服务/店铺",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户收藏服务/店铺</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"id\": 1,\n\"type\":1\n}",        "type": "POST"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "id",            "description": "<p>服务/店铺 id</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "type",            "description": "<p>收藏类别：1 服务；2| 店铺</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Collection.php",    "groupTitle": "MINI",    "name": "PostApiV1CollectionSave"  },  {    "type": "POST",    "url": "/api/v1/demand/save",    "title": "8-商家新增服务",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序商家新增服务</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"c_id\": 1,\n\"name\": \"修电脑\",\n\"area\": \"天河区\",\n\"price\": 500,\n\"unit\": \"次\",\n\"cover\": \"kdkmaskdmls;,ls;,\",\n\"des\": \"什么电脑都会修\",\n\"extend\": 1,\n\"imgs\": \"1,2,3\",\n}",        "type": "post"      }    ],    "parameter": {      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "c_id",            "description": "<p>类别id</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "name",            "description": "<p>服务名称</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "des",            "description": "<p>服务描述</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "area",            "description": "<p>区</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "price",            "description": "<p>价格</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "unit",            "description": "<p>单位</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "cover",            "description": "<p>封面图 base64</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "extend",            "description": "<p>是否推广：1 | 推广；2 | 不推广</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "imgs",            "description": "<p>图片id，多个用逗号隔开</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Shop.php",    "groupTitle": "MINI",    "name": "PostApiV1DemandSave"  },  {    "type": "POST",    "url": "/api/v1/demand/save",    "title": "5-用户新增需求",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户新增需求</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"name\": \"朱明良\",\n\"phone\": \"18956225230\",\n\"des\": \"修马桶\",\n\"province\": \"广东省\",\n\"city\": \"广州市\",\n\"area\": \"天河区\",\n\"address\": \"石城大道\",\n\"time_begin\": \"23:02:40\",\n\"time_end\": \"23:02:43\",\n\"money\": \"10000\",\n\"type\": \"1\",\n\"imgs\": \"1,2,3\",\n}",        "type": "post"      }    ],    "parameter": {      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "name",            "description": "<p>发布人</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "phone",            "description": "<p>联系方式</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "des",            "description": "<p>需求描述</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "province",            "description": "<p>省</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "city",            "description": "<p>市</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "area",            "description": "<p>区</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>详细地址</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "time_begin",            "description": "<p>开始时间</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "time_end",            "description": "<p>结束时间</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "money",            "description": "<p>金额，标准单位为分</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "type",            "description": "<p>需求类别：1 | 维修；2 | 家政</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "imgs",            "description": "<p>图片id，多个用逗号隔开</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Demand.php",    "groupTitle": "MINI",    "name": "PostApiV1DemandSave"  },  {    "type": "POST",    "url": "/api/v1/message/save",    "title": "9-用户给平台留言",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户给平台留言提供意见</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"msg\": \"小程序真好用\",\n\"email\": \"353575156@qq.com\",\n\"phone\": \"18956225230\",\n}",        "type": "POST"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>留言内容</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "email",            "description": "<p>邮箱（选填）</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "phone",            "description": "<p>联系方式</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Message.php",    "groupTitle": "MINI",    "name": "PostApiV1MessageSave"  },  {    "type": "POST",    "url": "/api/v1/shop/apply",    "title": "6-用户发起申请开商铺",    "group": "MINI",    "version": "1.0.1",    "description": "<p>小程序用户发起申请开商铺</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n\"name\": \"维修小铺\",\n\"phone\": \"18956225230\",\n\"phone_sub\": \"13731872800\",\n\"province\": \"广东省\",\n\"city\": \"广州市\",\n\"area\": \"天河区\",\n\"address\": \"石城大道\",\n\"type\": \"1\",\n\"head_url\": \"dadasdsadfsfdasfasd\",\n\"imgs\": \"1,2,3\",\n\"id_number\": \"34272792931939123\",\n}",        "type": "post"      }    ],    "parameter": {      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "name",            "description": "<p>店铺名称</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "phone",            "description": "<p>商家手机号</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "province",            "description": "<p>省</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "city",            "description": "<p>市</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "area",            "description": "<p>区</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>详细地址</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "type",            "description": "<p>需求类别：1 | 维修；2 | 家政</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "imgs",            "description": "<p>商家资料图片id，多个用逗号隔开</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "head_url",            "description": "<p>头像，base64</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\": \"ok\",\"error_code\": 0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误代码 0 表示没有错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>操作结果描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Shop.php",    "groupTitle": "MINI",    "name": "PostApiV1ShopApply"  },  {    "type": "POST",    "url": "/api/v1/user/info",    "title": "2-小程序用户信息获取并解密和存储",    "group": "MINI",    "version": "1.0.1",    "description": "<p>后台用户登录</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"iv\": \"wx4f4bc4dec97d474b\",\n   \"encryptedData\": \"CiyLU1Aw2Kjvrj\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "iv",            "description": "<p>加密算法的初始向量</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "encryptedData",            "description": "<p>包括敏感数据在内的完整用户信息的加密数据</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"msg\":\"ok\",\"errorCode\":0}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "error_code",            "description": "<p>错误码： 0表示操作成功无错误</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "msg",            "description": "<p>信息描述</p>"          }        ]      }    },    "filename": "application/api/controller/v1/User.php",    "groupTitle": "MINI",    "name": "PostApiV1UserInfo"  },  {    "type": "GET",    "url": "/api/v1/token/admin",    "title": "3-CMS获取登陆token",    "group": "PC",    "version": "1.0.1",    "description": "<p>后台用户登录</p>",    "examples": [      {        "title": "请求样例:",        "content": "{\n   \"phone\": \"18956225230\",\n   \"pwd\": \"a123456\"\n }",        "type": "post"      }    ],    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "phone",            "description": "<p>用户手机号</p>"          },          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "pwd",            "description": "<p>用户密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"u_id\":1,\"username\":\"管理员\",\"token\":\"bde274895aa23cff9462d5db49690452\"}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "u_id",            "description": "<p>用户id</p>"          },          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "username",            "description": "<p>管理员名称</p>"          },          {            "group": "返回参数说明",            "type": "String",            "optional": false,            "field": "token",            "description": "<p>口令令牌，每次请求接口需要传入，有效期 2 hours</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Token.php",    "groupTitle": "PC",    "name": "GetApiV1TokenAdmin"  },  {    "type": "POST",    "url": "/api/v1/image/save",    "title": "图片上传",    "group": "PC",    "version": "1.0.1",    "description": "<p>@apiExample {post}  请求样例: { &quot;base64&quot;: &quot;4f4bc4dec97d474b&quot; }</p>",    "parameter": {      "fields": {        "请求参数说明": [          {            "group": "请求参数说明",            "type": "String",            "optional": false,            "field": "base64",            "description": "<p>图片base64位编码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "返回样例:",          "content": "{\"id\":17}",          "type": "json"        }      ],      "fields": {        "返回参数说明": [          {            "group": "返回参数说明",            "type": "int",            "optional": false,            "field": "id",            "description": "<p>图片id</p>"          }        ]      }    },    "filename": "application/api/controller/v1/Image.php",    "groupTitle": "PC",    "name": "PostApiV1ImageSave"  }] });
