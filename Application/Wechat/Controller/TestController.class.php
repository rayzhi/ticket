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

    public function testCouponAct(){
        \Wechat\Logic\CouponLogic::giveCoupon(getOpenid(),1);
    }

    public function testPayCouponAct(){
        \Wechat\Logic\CouponLogic::givePayInvertCoupon(getOpenid());
    }

    public function testReceviceAct(){
        $couponlist = array(ShareCoupon1,ShareCoupon2,ShareCoupon3,ShareCoupon4,ShareCoupon5);
        shuffle($couponlist);
        $coupon = array_pop($couponlist);
        print_r($coupon);
        print_r($couponlist);
    }

    public function testClearCacheAct(){
        $dd = array('sada','asd','111');
        $dd = array_merge($dd ,false);
        print_r($dd);
    }

    public function testAllCouponAct(){
        echo getOpenid();
        $dd = \Wechat\Logic\CouponLogic::getAllCoupon(getOpenid());
        print_r($dd);
    }

    public function testUsortAct(){
        $aa[] = array('name'=>'aaa','price'=>55);
        $aa[] = array('name'=>'bbb','price'=>5);
        $aa[] = array('name'=>'ccc','price'=>15);
        $aa[] = array('name'=>'ddd','price'=>25);
        $aa[] = array('name'=>'eee','price'=>35);
        $aa[] = array('name'=>'fff','price'=>85);
        usort($aa, function($a,$b){
            $pa = $a['price'];
            $pb = $b['price'];
            return $pb-$pa;
        });
        print_r($aa);
    }

}
