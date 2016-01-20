<?php
namespace Wechat\Logic; 

class UserLogic{

    public static function saveUserInfo($userInfo){
        
        $check = D('User')->where(array('open_id'=>$userInfo['openid']))->find();
        if(!$check){
            $array['open_id']    = $userInfo['openid'];
            $array['headimgurl'] = $userInfo['headimgurl'];
            $array['nickname']   = $userInfo['nickname'];
            $array['sex']        = $userInfo['sex'];
            $array['ctime']      = time();
            $array['country']    = $userInfo['country'];
            $array['province']   = $userInfo['province'];
            $array['city']       = $userInfo['city'];
            
            D('User')->add($array);
        }
        
    }

}