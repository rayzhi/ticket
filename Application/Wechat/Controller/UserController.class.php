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
        
        $statusList = array();
        $ticketNo = array();
        foreach($orderSn as $k=>$v){
        	$statusList[] = $v['status'];
        	$ticketNo[] = $v['ticket_sn'];
        }
        
        $check = in_array('0',$statusList);
        //查看票是否已经使用
        if($check){
        	$ticketNos = implode(',',$ticketNo);
        	$taoPiao = R('Api/ticket_use_time',array($ticketNos));
        	if($taoPiao){
        		foreach($orderSn as $k=>$v){
        			foreach($taoPiao['data'] as $t=>$r){
        				if($v['ticket_sn'] == $r['ticketNo'] && $r['useTime']){
        					$orderSn[$k]['statusName'] = '已使用';
        					$this->where(array('ticket_sn'=>$v['ticket_sn']))->save(array('status'=>1));
        				}
        			}
        		}
        	}
        }
        
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

   public function wifiAct(){
        $this->display();
   }

   public function wifiChengduAct(){
        $this->display();
   }

}
