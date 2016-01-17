<?php
namespace Wechat\Handler; 

class TestHandler extends CommonHandler{

    public function handleText($msg){
        $text = $msg->getRevContent();
        if($text=="test"){
            $msg->text(C('WECHAT_WELCOME'))->reply();
            return;
        }
        else if($text=="初始化目录"){
            \Wechat\Logic\MenuLogic::initMenu();
            $msg->text("初始化成功");
            return;
        }
        else{
           return $this->next(); 
        }
        
    }

}