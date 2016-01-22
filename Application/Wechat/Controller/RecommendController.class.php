<?php

/**
 * 推荐有奖模块
 * 
 */
namespace Wechat\Controller;

class RecommendController extends CommonController {

    public function indexAct(){
        $weobj = wechatInstance();
        $signature = $weobj->getJsSign(currentUrl(),time(),md5(rand(1,9999)),C('WECHAT_APPID'));
        $signature['jsApiList'] = ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'];
        //$signature['debug'] = true;
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo(getOpenid());
        $this->assign('userinfo',$userinfo);

        $this->assign('inviteurl',domainurl().UC('Wechat/Coupon/reciveCoupon'));
        $this->assign('invitetext',getSysConfig('recommend-text'));
        $this->assign('signature',str_replace("\\/", "/", json_encode($signature)));
        $this->assign('activity_text',getSysConfig('activity-text'));
        $this->display();
    }

    
   

}