<?php

/**
 * 推荐有奖模块
 * 
 */
namespace Wechat\Controller;

class RecommendController extends CommonController {

    public function indexAct(){
        $weobj = wechatInstance();
        $signature = $weobj->getJsSign(currentUrl(),time(),rand(1,9999),C('WECHAT_APPID'));
        $signature['jsApiList'] = ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'];
        $this->assign('inviteurl',domainurl().U('/Ticket/index?inviter='.getOpenid()));
        $this->assign('invitetext',getSysConfig('recommend-text'));
        $this->assign('signature',str_replace("\\/", "/", json_encode($signature)));
        $this->display();
    }


    //邀请新用户
    public function invitedAct(){
        
    }
    
   

}
