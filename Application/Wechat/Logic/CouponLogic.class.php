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
        return $id;
    }

    //获取用户优惠券列表
    public static function getUserCoupon($openid){
        $now = time();
        $userclist = D('user_coupon')
            ->join("coupon on user_coupon.coupon_id=coupon.id")
            ->where("user_coupon.open_id='$openid' and coupon.end_time>$now")
            ->select();
        return $userclist;
    }


    //添加优惠券
    public static function addCoupon($couponinfo){
        $id = D('coupon')->add($couponinfo);
        return $id;
    }

    //获取已经拥有的优惠券个数
    public static function countCoupon($openid,$couponid=0){
        if($couponid==0){
            return D('user_coupon')->where(array('open_id'=>$openid))->count(1);
        }
        return D('user_coupon')->where(array('open_id'=>$openid,'coupon_id'=>$couponid))->count(1);
    }
   

}
