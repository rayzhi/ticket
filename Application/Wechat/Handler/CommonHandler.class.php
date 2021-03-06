<?php
namespace Wechat\Handler; 

class CommonHandler{

    //指示Handler继续往下执行
    public function next(){
        return 1;
    }
    public function handleText($msg){
        return $this->next();
    }
    public function handleImage($msg){
        return $this->next();
    }
    public function handleLocation($msg){
        return $this->next();
    }
    public function handleLink($msg){
        return $this->next();
    }
    public function handleEvent($msg){
        return $this->next();
    }
    public function handleMusic($msg){
        return $this->next();
    }
    public function handleNews($msg){
        return $this->next();
    }
    public function handleVoice($msg){
        return $this->next();
    }
    public function handleVideo($msg){
        return $this->next();
    }
}