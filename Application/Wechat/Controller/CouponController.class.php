<?php

/**
 * 优惠券模块
 * 
 */
namespace Wechat\Controller;

class CouponController extends CommonController {

    public function indexAct(){
        $list = \Wechat\Logic\CouponLogic::getUserCoupon(getOpenid());
        $activity_couponlist = D('user_activitycoupon')->where(array('open_id'=>getOpenid()))->select();
        foreach($activity_couponlist as $actinfo){
            $acdd = D('activity_coupon')->where(array('id'=>$actinfo['activitycoupon_id']))->find();
            $acinfo['title'] = $acdd['name'];
            $acinfo['price'] = $acdd['price'];
            $acinfo['begin_time'] = $acdd['stime'];
            $acinfo['end_time'] = $acdd['etime'];
            $acinfo['status'] = $acdd['status'];
            $actilist[] = $acinfo;
        }
        if($actilist){
            $list = array_merge($list,$actilist);
        }
        
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
                    \Wechat\Logic\PushLogic::pushTextMsg($openid,"恭喜您领到您的好友@".$invatename."分享的魔乐城优惠劵，优惠劵能抵消魔乐城场馆票价，记得使用哦！");
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
