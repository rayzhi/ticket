<?php

/**
 * 优惠券逻辑
 * 
 */
namespace Wechat\Logic;

class CouponLogic{

    //用户获取优惠券
    public static function giveCoupon($openid,$coupon_id){
        $info['sn'] = date('Ymd',time()) . (rand(1,1000) + 1000);
        $info['open_id'] = $openid;
        $info['coupon_id'] = $coupon_id;
        $info['status'] = 0;
        $info['order_id'] = 0;
        $info['coupon_pay'] = 0;
        $info['gettime'] = time();
        $info['usetime'] = 0;
        $info['coupon_pay'] = 0;
        $info['ctime'] = time();
        $info['note'] = "";
        $id = D('user_coupon')->add($info);

        //设置提示
        self::ticketTip($openid);
        return $id;
    }

    //用户支付后，给邀请人优惠券
    public static function givePayInvertCoupon($openid){
        if(!$openid) return;
        //先判断$openid是不是第一次支付成功
        $ordercount = D('ticket_order')->where(array('open_id'=>$openid,'status'=>1))->count(1);
        if($ordercount != 1){
            return;
        }
        $inviter = D('User')->where(array('open_id'=>$openid))->getField('inviter');
        if(!$inviter) return;
        self::giveCoupon($inviter,InviteCouponID2);
    }

    //获取用户普通优惠券列表
    public static function getUserCoupon($openid){
        $now = time();
        $userclist = D('user_coupon')
            ->join("coupon on user_coupon.coupon_id=coupon.id")
            ->where("user_coupon.open_id='$openid' and coupon.end_time>$now and user_coupon.status=0")
            ->field("coupon.title,coupon.price,coupon.begin_time,coupon.end_time,user_coupon.status,user_coupon.id")
            ->select();
        return $userclist;
    }


    //添加优惠券
    public static function addCoupon($couponinfo){
        $id = D('coupon')->add($couponinfo);
        return $id;
    }

    //获取已经拥有的普通优惠券个数
    public static function countCoupon($openid,$couponid=0){
        if($couponid==0){
            return D('user_coupon')->where(array('open_id'=>$openid))->count(1);
        }
        return D('user_coupon')->where(array('open_id'=>$openid,'coupon_id'=>$couponid))->count(1);
    }



    //发布分享优惠券
    public static function publicShareCoupon($openid){
        $share = S('sharecoupon_'.$openid);
        if($share) return;
        $couponlist = array(ShareCoupon1,ShareCoupon2,ShareCoupon3,ShareCoupon4,ShareCoupon5);
        shuffle($couponlist);
        S('sharecoupon_'.$openid,$couponlist);
    }

    //领取分享优惠券
    public static function receviceShareCoupon($receter,$inviter){
        if($receter == $inviter){
            return;
        }
        $readyusers = S('sharecoupon_user'.$inviter);
        if(in_array($receter, $readyusers)){
            //如果已经领取就直接返回
            return;
        }
        $couponlist = S('sharecoupon_'.$inviter);
        $coupon = array_pop($couponlist);
        if(!$coupon) return;
        self::giveCoupon($inviter,$coupon);

        //$invatename = D('User')->where(array('open_id'=>$receter))->getField('nickname');
        \Wechat\Logic\PushLogic::pushTextMsg($inviter,getSysConfig('coupon-text'));
        
        //更新优惠券
        S('sharecoupon_'.$inviter,$couponlist);
        //更新领取人
        $readyusers[] = $receter;
        S('sharecoupon_user'.$inviter,$readyusers);
    }

    //获取所有优惠券，包括普通优惠券，活动优惠券
    public static function getAllCoupon($openid){
        $list = self::getUserCoupon($openid);
        $activity_couponlist = D('user_activitycoupon')->where(array('open_id'=>$openid,'status'=>0))->select();
        foreach($activity_couponlist as $actinfo){
            $acdd = D('activity_coupon')->where(array('id'=>$actinfo['activitycoupon_id']))->find();
            $acinfo['title'] = $acdd['name'];
            $acinfo['price'] = $acdd['price'];
            $acinfo['begin_time'] = $acdd['stime'];
            $acinfo['end_time'] = $acdd['etime'];
            $acinfo['status'] = $acdd['status'];
            $acinfo['id'] = $actinfo['id'];
            $acinfo['coutype'] = 1;
            $actilist[] = $acinfo;
        }
        if($actilist){
            $list = array_merge($list,$actilist);
        }
        usort($list, function($a,$b){
            $pa = $a['price'];
            $pb = $b['price'];
            return $pb-$pa;
        });
        return $list;
    }

    //是否有优惠券
    public static function isHasCoupon($openid){
        $coupon = D('user_coupon')
            ->where("open_id='$openid' and status=0")
            ->find();
        $activitycoupon = D('user_activitycoupon')->where("open_id='$openid' and status=0")->find();
        if($coupon || $activitycoupon){
            return true;
        }
        return false;
    }

    //获得优惠券的价格,$couponType == 1 是活动优惠券，==0 是普通优惠券
    public static function getCouponPrice($couponId,$couponType){
        if($couponType == 0){
            $couponId = D('user_coupon')->where(array('id'=>$couponId))->getField('coupon_id');
            return D('coupon')->where(array('id'=>$couponId))->getField('price');
        }
        else if($couponType == 1){
            $couponId = D('user_activitycoupon')->where(array('id'=>$couponId))->getField('activitycoupon_id');
            return D('activity_coupon')->where(array('id'=>$couponId))->getField('price');
        }
    }


    //修改优惠券的状态
    public static function changeStatus($couponId,$couponType,$orderId){
        if($couponType == 0){
            $cSave['status'] = 1;
            $cSave['usetime'] = time();
            $cSave['coupon_pay'] = self::getCouponPrice($couponId,$couponType);
            $cSave['order_id'] = $orderId;
            return $cResult = D('user_coupon')->where(array('id'=>$couponId))->save($cSave);
        }
        else if($couponType == 1){
            $cSave['status'] = 1;
            $cSave['usetime'] = time();
            $cSave['order_id'] = $orderId;
            return $cResult = D('user_activitycoupon')->where(array('id'=>$couponId))->save($cSave);
        }
    }

    //设置提示
    public static function ticketTip($openid){
        S('ticketTip_'.$openid,true,3600);
    }

    //判断提示
    public static function isTip($openid){
        $resp = S('ticketTip_'.$openid);
        S('ticketTip_'.$openid,null);
        return $resp;
    }
   

}
