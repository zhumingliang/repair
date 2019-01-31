<?php


namespace app\api\service;


use app\api\model\LogT;
use app\api\model\ShopT;
use app\api\model\UserT;
use app\lib\enum\CommonEnum;
use app\lib\enum\RedEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use app\api\model\UserT as UserModel;
use think\facade\Cache;
use zml\tp_tools\Curl;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;
    protected $USER_MSG_IS_OK = 1;
    protected $USER_MSG_IS_NULL = 2;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(
            config('wx.login_url'),
            $this->wxAppID, $this->wxAppSecret, $this->code);
    }


    /**
     * @return array
     * @throws TokenException
     * @throws WeChatException
     * @throws \app\lib\exception\RedException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get()
    {
        $result = Curl::get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new WeChatException([
                'msg' => '获取session_key及openID时异常，微信内部错误',
                'errorCode' => '40000'
            ]);
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            } else {
                return $this->grantToken($wxResult);
            }
        }
    }

    /**
     * 获取token并缓存数据
     * @param $wxResult
     * @return array
     * @throws TokenException
     * @throws \app\lib\exception\RedException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function grantToken($wxResult)
    {

        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        $red_money = 0;
        if (!$user) {
            $u_id = $this->newUser($openid);
            $red_money = RedService::addRed(RedEnum::FIRST_LOGIN, $u_id);
        } else {
            $u_id = $user->id;
            if ($user && $user->state == 2) {
                throw new TokenException([
                    'msg' => '小程序用户被禁用，请联系平台',
                    'errorCode' => 20010
                ]);
            }

            //检测用户是否生成邀请码
            if (!strlen($user->code)) {
                $user->code = generateCode(1)[0];
                $user->save();
            }
        }


        UserT::where('openid', $openid)
            ->inc('login_count', 1)->update();

        //将获取用户信息的session_key存储
        $this->session_keyToCache($wxResult);
        $cachedValue = $this->prepareCachedValue($wxResult, $u_id);
        $token = $this->saveToCache($cachedValue);

        if (empty($cachedValue['nickName']) && empty($cachedValue['province'])) {
            return [
                'token' => $token,
                'code' => $cachedValue['code'],
                'type' => $this->USER_MSG_IS_NULL,
                'shop_id' => $cachedValue['shop_id'],
                'city' => $cachedValue['city'],
                'province' => $cachedValue['province'],
                'area' => $cachedValue['area'],
                'red_money' => $red_money,
            ];

        }


        return [
            'token' => $token,
            'type' => $this->USER_MSG_IS_OK,
            'code' => $cachedValue['code'],
            'shop_id' => $cachedValue['shop_id'],
            'city' => $cachedValue['city'],
            'province' => $cachedValue['province'],
            'area' => $cachedValue['area'],
            'red_money' => $red_money
        ];
    }

    /**
     * @param $cachedValue
     * @return string
     * @throws TokenException
     */
    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $request = Cache::remember($key, $value, $expire_in);
        //$request = Redis::instance()->set($key, $value, $expire_in);
        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存用户数据异常',
                'errorCode' => 20002
            ]);
        }
        return $key;
    }

    /**
     * 缓存session_key
     * @param $wxResult
     * @throws TokenException
     */
    private function session_keyToCache($wxResult)
    {
        $key = $wxResult['openid'];
        $value = $wxResult['session_key'];
        $expire_in = config('setting.session_key_expire_in');
        $request = Cache::remember($key, $value, $expire_in);
        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存session_key异常',
                'errorCode' => 20001
            ]);
        }
        //return $key;
    }

    /**
     * @param $wxResult
     * @param $u_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function prepareCachedValue($wxResult, $u_id)
    {
        $cachedValue = $wxResult;
        $cachedValue['shop_id'] = 0;
        $cachedValue['province'] = '';
        $cachedValue['city'] = '';
        $cachedValue['area'] = '';
        $user = UserModel::where('id', $u_id)
            ->find();

        $shop_info = ShopT::where('u_id', $u_id)
            ->where('frozen', CommonEnum::STATE_IS_OK)
            ->whereIn('state', [2, 4])
            ->find();
        if ($shop_info) {
            $cachedValue['shop_id'] = $shop_info->id;
            $cachedValue['province'] = $shop_info->province;
            $cachedValue['city'] = $shop_info->city;
            $cachedValue['area'] = $shop_info->area;
        }
        $cachedValue['u_id'] = $u_id;
        $cachedValue['phone'] = $user['phone'];
        $cachedValue['openId'] = $user['openId'];
        $cachedValue['gender'] = $user['gender'];
        $cachedValue['nickName'] = $user['nickName'];
        $cachedValue['name_sub'] = $user['name_sub'];
        $cachedValue['avatarUrl'] = $user['avatarUrl'];
        $cachedValue['code'] = $user['code'];
        return $cachedValue;
    }

    /**
     * @param $openid
     * @return mixed
     */
    private function newUser($openid)
    {
        $data = [
            'openId' => $openid,
            'code' => generateCode(1)[0]
        ];
        $user = UserT::create($data);
        return $user->id;
    }

    /**
     * @param $wxResult
     * @throws WeChatException
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }


}