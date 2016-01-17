<?php

namespace Common\Lib\Pclass;
use \Common\Lib\Pclass as t;

defined( 'IN_HEAVEN' ) or die ( 'Hacking Attempt!' );

class HuyiSmsService {

    const ACCOUNT = "cf_gzsd";
    const PASSWORD = "Ysb@gzsd123";

    /**
     * 互亿无线短信发送接口(http://www.ihuyi.com/index.php)
     * @param String $phone 手机号码
     * @param String $code  验证码
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSms($phone,$code){
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $password = md5(HuyiSmsService::PASSWORD);
        $post_data = "account=".HuyiSmsService::ACCOUNT."&password=".$password."&mobile=".$phone."&content=".rawurlencode("您的验证码是：".$code."，此验证码10分钟内有效。");
        $gets =  self::xml_to_array(self::Post($post_data, $target));

        $result = false;
        if($gets['SubmitResult']['code'] == 2) $result = true;
        return $result;
    }

    /**
     * 互亿无线短信发送接口(http://www.ihuyi.com/index.php)
     * 发发除了验证码之外的短信
     * @param String $phone 手机号码
     * @param String $content  短信内容
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSmsContent($phone,$content){
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $password = md5(HuyiSmsService::PASSWORD);
        $post_data = "account=".HuyiSmsService::ACCOUNT."&password=".$password."&mobile=".$phone."&content=".rawurlencode($content);
        $gets =  self::xml_to_array(self::Post($post_data, $target));

        $result = false;
        if($gets['SubmitResult']['code'] == 2) $result = true;
        return $result;
    }

    private static function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

    private static function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = self::xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
}