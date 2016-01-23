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
        $openid = getOpenid();
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo($openid);
        $this->assign('userinfo',$userinfo);
        if(IS_POST){
            $phone = I('post.phone');
            if(preg_match("/1[3458]{1}\d{9}$/",$phone)){
                $receviCount = \Wechat\Logic\CouponLogic::countCoupon($openid,NewerCouponID);
                if($receviCount>0){
                    $this->success('您已经领取过该优惠券了', 'index');
                    return;
                    //$this->assign("msg","您已经领取过该优惠券了");
                }
                else{
                    \Wechat\Logic\UserLogic::updateByOpenid($openid,array('phone'=>$phone));
                    \Wechat\Logic\CouponLogic::giveCoupon($openid,NewerCouponID); //赠送优惠券
                    $this->success('领取成功', 'index');
                    return;
                }
            }
            else{
                $this->assign("msg","手机号码填写不正确");
            }
        }
        
        $this->display();
    }
    
   

}
