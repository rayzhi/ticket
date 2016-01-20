<?php

namespace Wechat\Model;
use Think\Model;

class TicketOrderDetailModel extends Model{
    
    protected $tableName = 'ticket_order_detail';
 
    public function makeDetail($postData,$order_id){
        
        $array['order_id']  = $order_id;
        $array['ticket_id'] = 1;
        $array['amount']    = $postData['amount'];
        
        return $this->add($array);

    }


}