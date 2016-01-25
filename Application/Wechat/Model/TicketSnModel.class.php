<?php

namespace Wechat\Model;
use Think\Model;

class TicketSnModel extends Model{
    
    protected $tableName = 'ticket_sn';
    CONST TICKET_SN = 'ticket_sn';
 
    public function addTicketSn($did,$sn,$qrcode,$expiry_date,$price,$ticketTypeId,$ticketTypeName){
        
        $array['did']              = $did;
        $array['ticket_sn']        = $sn;
        $array['qrcode']           = $qrcode;
        $array['expiry_date']      = strtotime($expiry_date);
        $array['t_price']          = $price;
        $array['ticket_type_id']   = $ticketTypeId;
        $array['ticket_type_name'] = $ticketTypeName;
       
        return $this->add($array);

    }


}