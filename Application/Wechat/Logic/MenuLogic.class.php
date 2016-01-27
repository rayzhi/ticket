<?php
namespace Wechat\Logic; 
use Wechat\Common\WechatWrap;

class MenuLogic{

    public static function initMenu($wechat=null){
        if(!$wechat){
            $wechat = wechatInstance();
        }
        return $wechat->createMenu(C('WECHAT_MENU'));
    }

    public static function removeMenu($wechat=null){
        if(!$wechat){
            $wechat = wechatInstance();
        }
        return $wechat->deleteMenu();
    }

}