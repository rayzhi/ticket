<?php
namespace Wechat\Logic; 
use Wechat\Common\WechatWrap;

class MenuLogic{

    public static function initMenu(){
        $wechat = wechatInstance();
        return $wechat->createMenu(json_encode(C('WECHAT_MENU')));
    }

}