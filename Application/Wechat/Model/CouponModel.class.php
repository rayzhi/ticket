<?php

namespace Wechat\Model;
use Think\Model;

class CouponModel extends Model{
    
    protected $tableName = 'coupon';
    const COUPON = 'coupon';
 
    public function getOneInfo($id){
        return $this->where(array('id'=>$id))->find();
    }
    
   


}