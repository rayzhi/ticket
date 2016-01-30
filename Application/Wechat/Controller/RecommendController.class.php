<?php

/**
 * 推荐有奖模块
 * 
 */
namespace Wechat\Controller;

class RecommendController extends CommonController {

    public function indexAct(){
        $weobj = wechatInstance();
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo(getOpenid());
        $this->assign('userinfo',$userinfo);

        $this->assign('invitetext',getSysConfig('recommend-text'));
        $this->assign('activity_text',getSysConfig('share-desc'));
        $this->display();
    }

    //发布分享优惠券
    public function releaseShareCouponAct(){
        if(IS_POST){
            $openid = getOpenid();
            \Wechat\Logic\CouponLogic::publicShareCoupon($openid);
            return true;
        }
        return false;
    }

    
   

}
