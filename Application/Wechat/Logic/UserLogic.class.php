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

    public static function getUserinfo($openid){
        $userinfo = D('User')->where(array('open_id'=>$openid))->find();
        if(!$userinfo['nickname']) {
            $userinfo['nickname'] = "游客";
        }
        if(!$userinfo['headimgurl']){
            $userinfo['headimgurl'] = __PUBLIC__."/images/tao3.png";
        }
        return $userinfo;
    }

    public static function updateByOpenid($openid,$update){
        D('User')->where(array("open_id"=>$openid))->save($update);
    }

    public static function is_exists($openid){
        $userinfo = D('User')->where(array('open_id'=>$openid))->find();
        if($userinfo) return true;
        return false;
    }


}