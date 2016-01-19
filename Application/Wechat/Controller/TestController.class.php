<?php

namespace Wechat\Controller;
use Think\Controller;
use Wechat\Logic\CouponLogic;

class TestController extends Controller {

    public function testMenuAct(){
        print_r(\Wechat\Logic\MenuLogic::initMenu());
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

}
