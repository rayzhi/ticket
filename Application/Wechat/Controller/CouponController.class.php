<?php

/**
 * 优惠券模块
 * 
 */
namespace Wechat\Controller;

class CouponController extends CommonController {

    public function indexAct(){
        $list = \Wechat\Logic\CouponLogic::getUserCoupon(getOpenid());
        $this->assign('list',$list);
        $this->display();
    }

    //领取优惠券
    public function reciveCouponAct(){
        if(IS_POST){
            $inviter = I('get.inviter');
            $openid = getOpenid();
            $phone = I('post.phone');
            if(preg_match("/1[3458]{1}\d{9}$/",$phone)){
                if($inviter){
                    \Wechat\Logic\CouponLogic::giveCoupon($inviter,InviteCouponID); //赠送邀请人优惠券
                }

                $receviCount = \Wechat\Logic\CouponLogic::countCoupon($openid,NewerCouponID);
                if($receviCount>0){
                    $this->assign("errormsg","您已经领取过该优惠券了");
                }
                else{
                    \Wechat\Logic\UserLogic::updateByOpenid($openid,array('phone'=>$phone));
                    \Wechat\Logic\CouponLogic::giveCoupon($inviter,NewerCouponID); //赠送本人优惠券
                }

            }
            else{
                $this->assign("errormsg","手机号码填写不正确");
            }
        }
        
        $this->display();
    }
    
   

}
