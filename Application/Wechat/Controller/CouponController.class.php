<?php

/**
 * 优惠券模块
 * 
 */
namespace Wechat\Controller;
use Wechat\Common\WechatWrap;

class CouponController extends CommonController {

    public function indexAct(){
        $list = \Wechat\Logic\CouponLogic::getAllCoupon(getOpenid());

        $weobj = WechatWrap::getInstance();
        $userinfo = $weobj->getUserInfo(getOpenid());
        $this->assign('subscribe',$userinfo['subscribe']);
        $this->assign('isTip',\Wechat\Logic\CouponLogic::isTip(getOpenid()));
        
        $this->assign('list',$list);
        $this->display();
    }

    //领取优惠券
    public function reciveCouponAct(){
        $openid = getOpenid();
        $invate = I('get.invate');
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo($openid);
        $this->assign('userinfo',$userinfo);
        //先领分享优惠券，然后判断是否已经领取了新人优惠券
        \Wechat\Logic\CouponLogic::receviceShareCoupon($openid,$invate);
        $receviCount = \Wechat\Logic\CouponLogic::countCoupon($openid,NewerCouponID);
        if($receviCount>0){
            redirect('/Wechat/Coupon/index', 0, '页面跳转中...');
            return;
        }
        //再领新人优惠券
        if(IS_POST){
            $phone = I('post.phone');
            if(preg_match("/1[3458]{1}\d{9}$/",$phone)){
                if($openid == $invate){
                    $this->assign("msg","本人不可领取");
                }
                else{
                    \Wechat\Logic\UserLogic::updateByOpenid($openid,array('phone'=>$phone,'inviter'=>$invate));
                    //赠送优惠券
                    \Wechat\Logic\CouponLogic::giveCoupon($openid,NewerCouponID); //给本人
                    \Wechat\Logic\CouponLogic::giveCoupon($invate,InviteCouponID1); //给推荐者

                    $username = D('User')->where(array('open_id'=>$openid))->getField('nickname');
                    $invatename = D('User')->where(array('open_id'=>$invate))->getField('nickname');
                    \Wechat\Logic\PushLogic::pushTextMsg($invate,"非常感谢您的分享，您的好友@".$username."成功领取了一张魔乐城优惠劵。");
                    \Wechat\Logic\PushLogic::pushTextMsg($openid,getSysConfig('coupon-text'));
                    redirect('/Wechat/Coupon/index', 0, '页面跳转中...');
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
