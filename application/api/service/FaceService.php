<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/13
 * Time: 7:08 PM
 */

namespace app\api\service;

use aipFace\AipFace;
use app\lib\exception\FaceException;

class FaceService
{
    static protected $instance;
    private $APP_ID = '';
    private $API_KEY = '';
    private $SECRET_KEY = '';

    public function __construct()
    {
        $this->APP_ID = config('face.APP_ID');
        $this->API_KEY = config('face.API_KEY');
        $this->SECRET_KEY = config('face.SECRET_KEY');


    }


    public static function instance()
    {
        return new FaceService();
    }

    /**
     * 检测图片是否合法
     * 是否为有效面部图片
     * @param $image
     * @return bool
     * @throws FaceException
     */
    public function detectFace($image)
    {
        $client = new AipFace($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
        // $image = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1539451081321&di=ed220d7aefcbf4798c760ae967b55232&imgtype=0&src=http%3A%2F%2Fimg3.iyiou.com%2FPicture%2F2017-03-08%2F58bf72b6cc970.jpg";
        $imageType = "URL";
        // 调用人脸检测
        $detect_res = $client->detect($image, $imageType);
        if (isset($detect_res['error_code']) && $detect_res['error_code']) {
            throw  new FaceException();
            // return false;
        }
        $result = $detect_res['result'];
        if ($result['face_num'] = 1) {
            $list = $result['face_list'];
            $face_probability = $list[0]['face_probability'];
            if ($face_probability < 0.9) {
                //return false;
                throw  new FaceException(
                    ['code' => 401,
                        'msg' => '图片检测失败，面部特征识别度底',
                        'errorCode' => 99002
                    ]
                );

            }
            return true;

        } else {
            //return false;
            throw  new FaceException(
                ['code' => 401,
                    'msg' => '图片检测失败',
                    'errorCode' => 99003
                ]
            );
        }


    }

    /**
     * 注册图片
     * @param $image
     * @param $groupId
     * @param $userId
     * @return array
     */
    public function register($image, $groupId, $userId)
    {
        // $image = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1539451081321&di=ed220d7aefcbf4798c760ae967b55232&imgtype=0&src=http%3A%2F%2Fimg3.iyiou.com%2FPicture%2F2017-03-08%2F58bf72b6cc970.jpg";
        //$userId = 4;

        $client = new AipFace($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
        $imageType = "URL";
        // 调用人脸注册
        $res = $client->addUser($image, $imageType, $groupId, $userId);
        if (isset($res['error_code']) && $res['error_code']) {
            return ['res' => false];
            // throw  new FaceException();
        }
        $result = $res['result'];
        if (!isset($result['face_token'])) {
            /* throw  new FaceException([
                 ['code' => 401,
                     'msg' => '上传图片到百度云人脸库失败',
                     'errorCode' => 99004
                 ]
             ]);*/
            return ['res' => false];

        }
        return ['res' => true, 'face_token' => $result['face_token']];

    }


    /**
     * 删除图片
     * @param $userId
     * @param $groupId
     * @param $faceToken
     * @return bool
     */
    public function deleteFace($userId, $groupId, $faceToken)
    {
        $client = new AipFace($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
        /* $userId = "user1";
         $groupId = "group1";
         $faceToken = "face_token_23123";*/
        $res = $client->faceDelete($userId, $groupId, $faceToken);
        if (isset($res['error_code']) && $res['error_code']) {
            return false;
        }

        return true;
    }

    /**
     * 匹配图片
     * @param $groupIdList
     * @param $image
     * @return mixed
     * @throws FaceException
     */
    public function searchFace($groupIdList, $image)
    {
        /* $image = "http://www.mxpcp.com/uploadfile/2014/1121/20141121011225192.jpg";
         $groupIdList = "3";*/
        $client = new AipFace($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
        $imageType = "URL";
        $res = $client->search($image, $imageType, $groupIdList);
        if (isset($res['error_code']) && $res['error_code']) {

            throw  new FaceException();
        }
        $result = $res['result'];
        if (!isset($result['face_token'])) {
            throw  new FaceException([
                ['code' => 401,
                    'msg' => '用户信息不存在',
                    'errorCode' => 99006
                ]
            ]);

        }
        $user = $result['user_list'][0];
        if ($user['score'] < 70) {
            throw  new FaceException([
                ['code' => 401,
                    'msg' => '用户信息不存在',
                    'errorCode' => 99006
                ]
            ]);
        }
        return $user['user_id'];
    }


}