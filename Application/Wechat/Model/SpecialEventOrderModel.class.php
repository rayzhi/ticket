<?php

namespace Wechat\Model;
use \Common\Lib\Model\OrderbaseModel;

class SpecialEventOrderModel extends OrderbaseModel{

    const DEV_ORDER_SN_TYPE  = 'YD';    //开发和本地环境批发采购sn类型
    const TEST_ORDER_SN_TYPE = 'YT';    //测试环境批发采购sn类型
    const API_ORDER_SN_TYPE  = '21';    //正式环境批发采购sn类型

    /**
     * 生成订单
     */
    public function makeOrder($order,$orderItems){
    	//开启事务
    	$this->startTrans();

        $orderId = $this->lock(true)->max('id');
        $orderId = $orderId+1;

        $i = strlen($orderId);
        $end = $orderId;

        if ($i<6) {
            while (strlen($end) < 6) {
                $end = '0'.$end;
            }
        }else{
            $end = substr($end, (strlen($end)-6));
        }

        //测试环境和本地环境sn
        $sn = self::DEV_ORDER_SN_TYPE.substr(date('Ymd'),3).$end;	
        $order['sn'] = $sn;

        $order_id = $this->add($order);		//生成订单
        $sql = $this->getLastSql();

        if ($order_id) {
            $orderItems['order_id'] = $order_id;
            $res = D('SpecialEventOrderItems')->add($orderItems);	//添加订单详情信息

            if ($res) {
            	$this->commit();
            	return $sn;
            }else{
            	$this->rollback();
            	return false;
            }
        }else{
        	$this->rollback();
        	return false;
        }
    }

    /**
     * 获取用户订单
     * @param $sn订单号
     * @param $openid 用户ID
     */
    public function getOrder($sn,$openid){
        $whereMap = array('sn'=>$sn,'openid'=>$openid);
        $order = $this->where($whereMap)->find();

        $orderItems = D('SpecialEventOrderItems')->getItems($order['id']);      //根据订单ID获取产品列表（ID和数量）

        $productData = D('SpecialEventProduct')->getProduct($orderItems['product_id']); //根据产品ID获取产品信息

        $data['sn']                 = $order['sn'];
        $data['name']               = $order['name'];
        $data['title']              = $productData['title'];
        $data['amout']              = $orderItems['amount'];
        $data['third_party_pay']    = $order['third_party_pay'];
        $data['address']            = $order['address'];
        $data['payments']           = array(array('paymentId' => 1,'payName' => '支付宝支付','payUrl' => 'http://wxb.ysbang.cn/static/competeorder/image/zhi.png'),
                                    array('paymentId' => 2,'payName' => '微信支付','payUrl' => 'http://wxb.ysbang.cn/static/competeorder/image/wechat_s.png'));    

        return $data;    
    }
}