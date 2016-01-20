<?php

/**
 * 优惠券逻辑
 * 
 */
namespace Wechat\Logic;

class CouponLogic{

    //用户获取优惠券
    public static function getCoupon($openid,$coupon_id){
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


    //添加优惠券
    public static function addCoupon($couponinfo){
        $id = D('coupon')->add($couponinfo);
        return $id;
    }
   

}
