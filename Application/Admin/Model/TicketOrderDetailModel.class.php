<?php

namespace Admin\Model;
use Think\Model;

class TicketOrderDetailModel extends Model {

    protected $tableName = 'ticket_order_detail';
    const TICKET_ORDER_DETAIL = 'ticket_order_detail';
    
    
    
    public function getOrderSn($order_id){
        
        $tbTicketSn = \Admin\Model\TicketSnModel::TICKET_SN;//需要数据表
        
        //药品各种参数
        $result =  $this->table(self::TICKET_ORDER_DETAIL.' a')
                        ->join('left join '.$tbTicketSn.' b ON a.did=b.did')
                        ->field('a.*,b.ticket_sn,b.qrcode,b.ticket_type_name,b.t_price')
                        ->where(array('order_id'=>$order_id))
                        ->select();  

        foreach($result as $k=>$v){
        	$result[$k]['etime'] = $v['expiry_date'] ? date('Y-m-d H:i:s',$v['expiry_date']) : '';
        }
    
        return $result;
    }
    



}

?>