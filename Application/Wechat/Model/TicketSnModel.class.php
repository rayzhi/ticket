<?php

namespace Wechat\Model;
use Think\Model;

class TicketSnModel extends Model{
    
    protected $tableName = 'ticket_sn';
    CONST TICKET_SN = 'ticket_sn';
 
    public function addTicketSn($did,$sn,$qrcode,$expiry_date){
        
        $array['did']         = $did;
        $array['ticket_sn']   = $sn;
        $array['qrcode']      = $qrcode;
        $array['expiry_date'] = strtotime($expiry_date);
       
        return $this->add($array);

    }


}