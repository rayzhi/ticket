<?php

namespace Common\Lib\Pclass;
use \Common\Lib\Pclass as t;

defined( 'IN_HEAVEN' ) or die ( 'Hacking Attempt!' );

class YsSmSService {

    const APPID = "328469860000039917";
    const APPSECRET = "772184ed9560a57e31027f733a63d9a6";
    const TEMPLATEID = "91004184";

    /**
     * 中国电信短信发送接口
     * @param String $phone 手机号码
     * @param String $code  验证码
     * @param Boolean  $flag  是否请求新令牌：true-请求(不管本地Redis是否保存令牌)  false-不请求(首先从本地Redis中取，没有的话再向服务器请求)
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSms($phone,$code,$flag = false){
        $url = "http://api.189.cn/v2/emp/templateSms/sendSms";

        $msgParam['code'] = $code;
        $msgParam['time'] = 10;          //分钟为单位

        $postData['app_id'] = YsSmSService::APPID;
        $postData['access_token'] = self::getAccessToken($flag);
        $postData['acceptor_tel'] = $phone;
        $postData['template_id'] = YsSmSService::TEMPLATEID;
        $postData['template_param'] = json_encode($msgParam);
        $postData['timestamp'] = urlencode(date('Y-m-d H:i:s', time()));

        foreach ($postData as $key => $value) {
            $post[] = $key . '=' . $value;
        }
        //$post = urlencode(implode('&',$post));
        $post = implode('&',$post);
        $curl = new t\Curl();
        $resultJson = $curl->post($url, $post);
        $resultData = json_decode($resultJson,true);  // 转为数组

        $result = false;
        if (!empty($resultData) && $resultData['res_code'] == 0) $result = true;
        if (!$flag && !$result && $resultData['res_code'] == 110) {  // 令牌已过期
            $flag = true;  // 防止陷入死循环
            $result = self::sendSms($phone,$code,$flag);
        }
        return $result;
    }

    /**
     * 获取发送短信的令牌
     * @param Boolean  $flag  是否请求新令牌：true-请求(不管本地Redis是否保存令牌)  false-不请求(首先从本地Redis中取，没有的话再向服务器请求)
     * @return String 令牌
     */
    private static function getAccessToken($flag){
        if (!$flag) {  // 首先从本地Redis中取
            $accesstoken = MyRedis::get('sms.accesstoken');
            if($accesstoken) return $accesstoken;
        }

        $url = "https://oauth.api.189.cn/emp/oauth2/v3/access_token";
        $postData['grant_type'] = 'client_credentials';
        $postData['app_id'] = YsSmSService::APPID;
        $postData['app_secret'] = YsSmSService::APPSECRET;

        foreach ($postData as $key => $value) {
            $post[] = $key . '=' . $value;
        }

        $curl = new t\Curl();
        $resultJson = $curl->post($url, implode('&',$post));
        $resObj = json_decode($resultJson);
        $expiers = intval($resObj->expires_in);
        $accesstoken = $resObj->access_token;
        if ($expiers > 0) MyRedis::set('sms.accesstoken',$accesstoken,$expiers);
        return $accesstoken;
    }
}