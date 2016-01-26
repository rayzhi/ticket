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
        $invate = I('get.invate');
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo($openid);
        $this->assign('userinfo',$userinfo);
        if(IS_POST){
            $phone = I('post.phone');
            if(preg_match("/1[3458]{1}\d{9}$/",$phone)){
                $receviCount = \Wechat\Logic\CouponLogic::countCoupon($openid,NewerCouponID);
                if($receviCount>0){
                    $this->success('您已经领取过该优惠券了', 'index');
                    return;
                }
                else{
                    if($openid == $invate){
                        $this->success('本人不可领取', 'index');
                        return;
                    }
                    \Wechat\Logic\UserLogic::updateByOpenid($openid,array('phone'=>$phone));
                    \Wechat\Logic\CouponLogic::giveCoupon($openid,NewerCouponID); //赠送优惠券

                    $username = D('User')->where(array('open_id'=>$openid))->getField('nickname');
                    $invatename = D('User')->where(array('open_id'=>$invate))->getField('nickname');
                    \Wechat\Logic\PushLogic::pushTextMsg($invate,"非常感谢您的分享，您的好友@".$username."成功领取了一张魔乐城优惠劵。");
                    \Wechat\Logic\PushLogic::pushTextMsg($invate,"恭喜您领到您的好友@".$invatename."分享的魔乐城优惠劵，优惠劵能抵消魔乐城场馆票价，记得使用哦！");
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
