<?php

/**
 * 优惠券模块
 * 
 */
namespace Wechat\Controller;

class ActivityController extends CommonController {

    public function indexAct(){
        $id = I('get.id');
        $info = D('activity')->where(array('id'=>$id))->find();
        $this->assign('info',$info);

        $this->assign('inviteurl',currentUrl());

        $this->display();
    }

    public function receiveAct(){
        $activity_id = I('get.id');
        $nowtime = time();
        $couponlist = D('activity_coupon')->where("activity_id=$activity_id and stime<$nowtime and etime>$nowtime")->select();
        foreach($couponlist as $coupon){
            $isexist = D('user_activitycoupon')->where(array('open_id'=>getOpenid(),'activitycoupon_id'=>$coupon['id']))->find();
            if($isexist){
                continue;
            }
            $info['open_id'] = getOpenid();
            $info['activitycoupon_id'] = $coupon['id'];
            $info['ctime'] = $nowtime;
            $info['status'] = 0;
            D('user_activitycoupon')->add($info);
            //小提示
            \Wechat\Logic\CouponLogic::ticketTip(getOpenid());
        }

        //设置提示

        \Wechat\Logic\PushLogic::pushTextMsg(getOpenid(),getSysConfig('coupon-text'));
        redirect(UC('/Wechat/Coupon/index'), 0, '页面跳转中...');
    }

}
