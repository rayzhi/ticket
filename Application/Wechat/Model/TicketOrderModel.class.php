<?php

namespace Wechat\Model;
use Think\Model;

class TicketOrderModel extends Model{
    
    protected $tableName = 'ticket_order';
    CONST TICKET_ORDER = 'ticket_order';
    
    var $orderStatus = array('未使用','已使用','已过期');
 
    public function makeOrder($postData){
        
        $array['sn']              = time().rand(100000,999999);
        $array['open_id']         = session('openid');
        $array['total_cost']      = $postData['price'];
        $array['add_time']        = time();
        $array['status']          = 0;
        $array['third_party_pay'] = $postData['price'];
        
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
    
    public function orderAllInfo($order_sn){
        
        $tbUser = \Wechat\Model\UserModel::USER;//需要数据表
        $tbTicketOrderDetail = \Wechat\Model\TicketOrderDetailModel::TICKET_ORDER_DETAIL;
        
        //药品各种参数
        $result =  $this->table(self::TICKET_ORDER.' a')
                        ->join('left join '.$tbUser.' b ON a.open_id=b.open_id')
                        ->join('left join '.$tbTicketOrderDetail.' c ON a.id=c.order_id')                    
                        ->field('a.*,b.nickname,b.sex,c.*')
                        ->where(array('a.sn'=>$order_sn))
                        ->find();
        
        return $result;
    }
    
    public function getOrderTicketSn($order_id){
        
        $tbTicketSn = \Wechat\Model\TicketSnModel::TICKET_SN;//需要数据表
        $tbTicketOrderDetail = \Wechat\Model\TicketOrderDetailModel::TICKET_ORDER_DETAIL;
        
        //药品各种参数
        $result =  $this->table(self::TICKET_ORDER.' a')
                        ->join('left join '.$tbTicketOrderDetail.' b ON a.id=b.order_id')
                        ->join('left join '.$tbTicketSn.' c ON b.did=c.did')
                        ->field('a.id,b.price,c.*')
                        ->where(array('a.id'=>$order_id))
                        ->select();
        
        foreach($result as $k=>$v){
            if($v['qrcode']){
                $result[$k]['qrurl'] = $v['qrcode'];
            }else{
                $qrcode = createQr($v['ticket_sn']);
                $result[$k]['qrurl'] = $qrcode;
                $cond['ticket_sn']   = $v['ticket_sn'];
                $cond['did']         = $v['did'];
                D('TicketSn')->where($cond)->save(array('qrcode'=>$qrcode));
            }   
            $result[$k]['statusName']  = $this->orderStatus[$v['status']];
            //过期修改状态为已使用
            if($v['status'] == 0 && $v['expiry_date'] && $v['expiry_date'] < time()){
            	$result[$k]['statusName']= '已过期';
            	$this->where(array('ticket_sn'=>$v['ticket_sn']))->save(array('status'=>2));
            	$result[$k]['status'] = 2;
            }            
            $result[$k]['expiry_date'] = $v['expiry_date'] ? date('Y-m-d H:i:s') : '';
            
        }
        return $result;
        
    }

    
    public function ticketPriceUseCoupon($order_sn){
    	
    	$tbTicketSn = \Wechat\Model\TicketSnModel::TICKET_SN;//需要数据表
    	$tbTicketOrderDetail = \Wechat\Model\TicketOrderDetailModel::TICKET_ORDER_DETAIL;
    	
    	//药品各种参数
    	$result =  $this->table(self::TICKET_ORDER.' a')
				    	->join('left join '.$tbTicketOrderDetail.' b ON a.id=b.order_id')
				    	->join('left join '.$tbTicketSn.' c ON b.did=c.did')
				    	->field('a.id,a.total_cost,a.tirhd_party_pay,c.*')
				    	->where(array('a.sn'=>$order_sn))
				    	->order('c.t_price DESC')
				    	->select();
    	
    	return $result;
    	
    }
    
    public function ifPushTicketMsg(){
        
        $tbTicketSn = \Wechat\Model\TicketSnModel::TICKET_SN;//需要数据表
        $tbTicketOrderDetail = \Wechat\Model\TicketOrderDetailModel::TICKET_ORDER_DETAIL;
                 $cond['a.status']  = 1;
        $cond['c.status']  = 0;
        $cond['c.expiry_date'] = array('between',array(time()-60*60*48,time()));
        
        //药品各种参数
        $result =  $this->table(self::TICKET_ORDER.' a')
                        ->join('left join '.$tbTicketOrderDetail.' b ON a.id=b.order_id')
                        ->join('left join '.$tbTicketSn.' c ON b.did=c.did')
                        ->where($cond)
                        ->field('a.open_id as open_id,c.did as sn')
                        ->find();
         
        return $result ? true : false;
        
    }

    

}