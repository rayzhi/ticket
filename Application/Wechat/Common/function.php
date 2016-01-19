<?php

function wechatInstance(){
    return \Wechat\Common\WechatWrap::getInstance();
}

function recordLog($data,$fileName){
    return \Common\Lib\Pclass\Webown::record_log(print_r($data,true),$fileName);
}



