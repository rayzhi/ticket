<?php
namespace Wechat\Handler; 

class DefaultHandler extends CommonHandler{

    public function handleText($msg){
        return $this->next(); 
    }

    public function handleEvent($msg){
        //订阅事件
        if($msg->getRevEvent() == "subscribe"){
            $msg->text(C('WECHAT_WELCOME'))->reply(); //回复用户
            //保存新用户信息
            $userinfo = $msg->getUserInfo($msg->getRevFrom());
            \Wechat\Logic\UserLogic::saveUserInfo($userinfo);
            return;
        }
        
    }

}