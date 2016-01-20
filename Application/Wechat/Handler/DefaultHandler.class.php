<?php
namespace Wechat\Handler; 

class DefaultHandler extends CommonHandler{

    public function handleText($msg){
        return $this->next(); 
    }

    public function handleEvent($msg){
        $msg->text(C('WECHAT_WELCOME'))->reply();
        $userinfo = $msg->getUserInfo($msg->getRevFrom());
        slog(print_r($userinfo,true));
        return;
    }

}