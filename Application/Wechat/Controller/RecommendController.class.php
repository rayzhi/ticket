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
        $this->assign('activity_text',getSysConfig('activity-text'));
        $this->display();
    }


    //完成邀请
    public function invitedAct(){
        if(IS_POST){
            $openid = getOpenid();
            \Wechat\Logic\CouponLogic::giveCoupon($openid,InviteCouponID); //赠送邀请优惠券
            echo 1;
        }
        echo 0;
        
    }
    
   

}
