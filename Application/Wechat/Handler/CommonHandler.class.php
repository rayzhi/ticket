<?php
namespace Wechat\Handler; 

class CommonHandler{

    //指示Handler继续往下执行
    public function next(){
        return 1;
    }
    public function handleText($msg){
        return next();
    }
    public function handleImage($msg){
        return next();
    }
    public function handleLocation($msg){
        return next();
    }
    public function handleLink($msg){
        return next();
    }
    public function handleEvent($msg){
        return next();
    }
    public function handleMusic($msg){
        return next();
    }
    public function handleNews($msg){
        return next();
    }
    public function handleVoice($msg){
        return next();
    }
    public function handleVideo($msg){
        return next();
    }
}