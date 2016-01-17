<?php

namespace Common\Lib\Pclass;
defined( 'IN_HEAVEN' ) or die ( 'Hacking Attempt!' );


/**--
 * 基础服务--验证码生成和验证
 * 
 * PHP version 5
 * 
 * @category    COM
 * @package     GZNC
 *

 ******************
 **/

class IdentifyCode{

    /**-------
     * 生成验证码
     * @param String $phone 手机号码
     * @param int $expire 过期时间，单位（秒）
     * @param int $type 业务类型  0-注册帐号 1-发送验证码到旧绑定手机 2-更换绑定手机 3-提现
     * @return String 六位数验证码
    ------*/
    public static function createCode($phone,$expire = 3600,$type = 0){
        $randNum = rand(1,999999);
        $randNum = sprintf("%06d", $randNum);
        MyRedis::set("identifyCode.pub:$type:$phone",$randNum,$expire);
        return $randNum;
    }


    /**-------
     * 检查验证码
     * @param String $phone 手机号码
     * @param String $code 验证码
     * @param int $type 业务类型
     * @return boolean 验证状态
    ------*/
    public static function identifyCode($phone,$code,$type = 0){
        $memCode = MyRedis::get("identifyCode.pub:$type:$phone");
        if($memCode != 0 && $memCode == $code){
            return true;
        }
        return false;
    }

}