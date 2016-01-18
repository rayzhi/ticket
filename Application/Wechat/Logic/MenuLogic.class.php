<?php
namespace Wechat\Logic; 
use Wechat\Common\WechatWrap;

class MenuLogic{

    public static function initMenu(){
        $wechat = wechatInstance();
        return $wechat->createMenu(C('WECHAT_MENU'));
    }

}