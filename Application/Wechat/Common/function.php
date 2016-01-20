<?php

function wechatInstance(){
    return \Wechat\Common\WechatWrap::getInstance();
}

//获得当前URL
function currentUrl(){
    return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
//获得项目根路径
function domainurl(){
    return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
}
function recordLog($data,$fileName){
    return \Common\Lib\Pclass\Webown::record_log(print_r($data,true),$fileName);
}


//获取数据库中sysconfig的配置值
function getSysConfig($key){
    return D('sysconfig')->where(array('ckey'=>$key))->getField('cvalue');
}

//获得openid
function getOpenid(){
    //return 555;
    return session('openid');
}

//调试日志
function slog($content){
    file_put_contents("./test.log",$content,FILE_APPEND);
}