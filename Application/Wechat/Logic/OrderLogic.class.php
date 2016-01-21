<?php
namespace Wechat\Logic; 

class OrderLogic{

    public static function getUserOrderList($openid){
        $list = D('ticket_order')->where(array("open_id"=>$openid))->select();
        foreach ($list as &$info) {
            $orderdetail = D('ticket_order_detail')->where(array('order_id'=>$info['id']))->find();
            $info['amount'] = $orderdetail['amount'];
            $info['name'] = D('ticket')->where(array('id'=>$orderdetail['ticket_id']))->getField('name');
        }
        return $list;
    }

}