<?php
namespace Wechat\Handler; 

class CommonHandler{

    //指示Handler继续往下执行
    public function next(){
        return 1;
    }
    public function handleText($msg){
        next();
    }
    public function handleImage($msg){
        next();
    }
    public function handleLocation($msg){
        next();
    }
    public function handleLink($msg){
        next();
    }
    public function handleEvent($msg){
        next();
    }
    public function handleMusic($msg){
        next();
    }
    public function handleNews($msg){
        next();
    }
    public function handleVoice($msg){
        next();
    }
    public function handleVideo($msg){
        next();
    }
}