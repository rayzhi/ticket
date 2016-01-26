<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class UserController extends CommonController {

    public function indexAct(){
        
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo(getOpenid());
        $this->assign('userinfo',$userinfo);
        
        $this->display();
        
    }
    
    public function orderDetailAct(){
        
        $order_id = I('order_id');
        if(!$order_id) $this->error('非法操作');
        $orderSn = D('TicketOrder')->getOrderTicketSn($order_id);
      
        $this->assign('orderSn',$orderSn);

        $this->display();
    }
    
    public function couponAct(){
        
        $coupon = D('UserCoupon')->getCoupon();
        $this->assign('coupon',$coupon);

        $this->display();
    }
    
   public function orderlistAct(){
        $list = \Wechat\Logic\OrderLogic::getUserOrderList(getOpenid());
        $this->assign('list',$list);
        $this->display();
   }

   public function activityInfoAct(){
        $this->assign('activity_text',getSysConfig('activity-text'));
        $this->display();
   }

   public function pushNotifyAct(){
        $pushlist = D('TicketOrder')->ifPushTicketMsg();
        foreach($pushlist as $push){
            $issend = S('push_'.$push['open_id'].'_'.$push['sn']);
            if($issend){
                continue;
            }
            \Wechat\Logic\PushLogic::pushTextMsg("魔乐城票务提醒：\n  尊敬的用户：您购买的魔乐城体验票还有2天就要过期了，请尽快入场消费。感谢您的支持，祝您玩的愉快！");
            S('push_'.$push['open_id'].'_'.$push['sn'],true);
        }
   }

}
