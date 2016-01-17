<?php
namespace Wechat\Handler; 

class TestHandler extends CommonHandler{

    public function handleText($msg){
        $msg->text(C('WECHAT_WELCOME'))->reply();
        next();
    }

}