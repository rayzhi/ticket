<?php

namespace Wechat\Model;
use Think\Model;

class TicketOrderDetailModel extends Model{
    
    protected $tableName = 'ticket_order_detail';
    CONST TICKET_ORDER_DETAIL = 'ticket_order_detail';
 
    public function makeDetail($postData,$order_id){
        
        $array['order_id']         = $order_id;
        $array['venues_id']        = $postData['venues_id'];
        $array['amount']           = $postData['amount'];
        $array['price']            = $postData['price'];
        $array['ticket_main_type'] = $postData['main_type']; //1是团票  0 普通票
        $array['ticket_type']      = $postData['ticket_type_id'];
        $array['expiry_date']      = time()+60*60*24*30;
        $array['ticket_name']      = $postData['ticketName'];
        
        return $this->add($array);

    }


}