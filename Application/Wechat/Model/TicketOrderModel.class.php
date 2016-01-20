<?php

namespace Wechat\Model;
use Think\Model;

class TicketOrderModel extends Model{
    
    protected $tableName = 'ticket_order';
 
    public function makeOrder($postData){
        
        $ticketInfo = D('Ticket')->where(array('id'=>$postData['ticket_id']))->find();
        
        $array['sn']              = time().rand(100000,999999);
        $array['open_id']         = session('openid');
        $array['total_cost']      = $ticketInfo['price'] * $postData['amount'];
        $array['add_time']        = time();
        $array['status']          = 0;
        $array['third_party_pay'] = $ticketInfo['price'] * $postData['amount'];
        
        $order_id = $this->add($array);
        if($order_id){
            $data = D('TicketOrderDetail')->makeDetail($postData,$order_id);
            return $order_id;
        }else{
            return false;
        }
        
    }
    
    public function getOrderInfo($order_id){
        return $this->where(array('id'=>$order_id))->find();
    }


}