<?php
namespace Wechat\Logic; 

class OrderLogic{

    public static function getUserOrderList($openid){
        $list = D('ticket_order')->where(array("open_id"=>$openid))->order('id DESC')->select();
        $venuesInfo = self::venuesInfo();
        foreach ($list as &$info) {
            $orderdetail = D('ticket_order_detail')->where(array('order_id'=>$info['id']))->find();
            $info['amount'] = $orderdetail['amount'];
            $info['name'] = $orderdetail['ticket_name'];
            $info['venuesName'] = $venuesInfo[$orderdetail['venues_id']];
        }
        return $list;
    }
    
    /**
     * 场馆信息
     * 目前只知道一个，其他后续再添加  
     */
    public static function venuesInfo(){
        return array(
            '3' => 'GOGOPARK恒福国际'
        );
    }

}