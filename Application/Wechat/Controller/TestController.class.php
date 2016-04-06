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
        print_r(date('Y-m-d',1454300937));
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

    /**
     * 测试返回价格
     */
    public function testReturnPriceAct(){
    	
        $order_sn = "1459907424700866";
        $snResult = D('TicketOrder')->ticketPriceUseCoupon($order_sn);
        recordLog($snResult,'api');
        if($snResult){

            $coupon = $snResult[0]['total_cost'] - $snResult[0]['third_party_pay'];

            foreach($snResult as &$ticket){
                $ticket['t_price'] = $ticket['t_price'] - $coupon;
                if($ticket['t_price']>0){
                    break;
                }
                $ticket['t_price'] = 0;
                $coupon = abs($ticket['t_price']);
            }
            
            foreach($snResult as $k=>$v){
                $result =R('Api/wxcallback',array($v['ticket_sn'],$v['t_price']));//返回票的价格
                recordLog($result,'api');
            }
            

        }
    }

    /**
     * 所有已支付的票号和实际支付价格
     */
    public function payPriceAct(){
        $readyPayList = D('TicketOrder')->where(array('status'=>1))->field('sn')->select();
        $rs = array();
        foreach($readyPayList as $info){
            $snResult = D('TicketOrder')->ticketPriceUseCoupon($info['sn']);
            if($snResult){
                $coupon = $snResult[0]['total_cost'] - $snResult[0]['third_party_pay'];
                foreach($snResult as &$ticket){
                    $ticket['t_price'] = $ticket['t_price'] - $coupon;
                    if($ticket['t_price']>0){
                        break;
                    }
                    $ticket['t_price'] = 0;
                    $coupon = abs($ticket['t_price']);
                }
                foreach($snResult as $ticket){
                    if(!$ticket['ticket_sn']) continue;
                    $rsinfo = array();
                    $rsinfo['sn'] = $ticket['ticket_sn'];
                    $rsinfo['price'] = $ticket['t_price'];
                    $rs[] = $rsinfo;
                }
            }
        }
        echo json_encode($rs);
    }

}
