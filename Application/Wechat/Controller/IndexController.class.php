<?php

namespace Wechat\Controller;
use Think\Controller;
use Wechat\Common\WechatWrap;

class IndexController extends Controller {

	/**
	 * 微信入口
	 */
    public function indexAct(){
        $weobj = WechatWrap::getInstance();

        //验证微信请求
        if ( isset($_GET["echostr"])){
            $weobj->valid();
            return;
        }
        //处理微信请求
        WechatWrap::handleMsg($weobj->getRev());
    }



    
    
}
