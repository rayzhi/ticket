<?php

namespace Wechat\Controller;
use Think\Controller;
use Wechat\Logic\CouponLogic;

class TestController extends Controller {

    public function testMenuAct(){
        print_r(\Wechat\Logic\MenuLogic::initMenu());
    }

    public function testRemoveMenuAct(){
        print_r(\Wechat\Logic\MenuLogic::removeMenu());
    }

    public function testAddCouponAct(){
        $couponinfo['title'] = "测试";
        $couponinfo['price'] = 50;
        $couponinfo['min_pay'] = 30;
        $couponinfo['type'] = 1;
        $couponinfo['note'] = "测试";
        $couponinfo['status'] = 1;
        $couponinfo['begin_time'] = time();
        $couponinfo['end_time'] = time();
        $couponinfo['ctime'] = time();
        print_r(CouponLogic::getCoupon(1,2));
    }

    public function testlogAct(){
        slog("45646546456");
    }

    public function testqrAct(){
        echo createQr("123456");
    }

    public function testPushAct(){
        $d = \Wechat\Logic\PushLogic::pushTextMsg('oZcK7wlC8I_MN-ye8RIeJg0SPWmo',"test");
        print_r($d);
    }

    public function testPayCouponAct(){
        \Wechat\Logic\CouponLogic::givePayInvertCoupon('oZcK7wtW4NB-hr5I1_XKKfoC6zV8');
    }

    public function testReceviceAct(){
        $couponlist = array(ShareCoupon1,ShareCoupon2,ShareCoupon3,ShareCoupon4,ShareCoupon5);
        shuffle($couponlist);
        $coupon = array_pop($couponlist);
        print_r($coupon);
        print_r($couponlist);
    }

    public function testClearCacheAct(){
    }
}
