<?php

namespace Wechat\Model;
use Think\Model;

class TicketSnModel extends Model{
    
    protected $tableName = 'ticket_sn';
    CONST TICKET_SN = 'ticket_sn';
 
    public function addTicketSn($did,$sn){
        
        $array['did'] = $did;
        $array['ticket_sn'] = $sn;
       
        return $this->add($array);

    }


}