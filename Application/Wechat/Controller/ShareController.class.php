<?php

/**
 * 支付宝
 * @author ysbang
 * 2015-04-24
 *
 */

namespace Wechat\Controller;
use \Common\Lib\Pay\pay_wap_wechat as p;

class ShareController extends CommonController {
	
	/**
	 * 分享购买课程
	 */
	public function courseAct(){
				
		//查询活动信息数据
		$data = D('SpecialEvent')->getSpecialEventData();

		$this->assign('data',$data);
		$this->display();
		
	}
	
	/**
	 * 支付
	 */
	public function doPayAct(){
		
		$order_id = I('id');
		if(!$order_id) $this->error('非法操作！');
		
		$orderInfo = D('SpecialEventOrder')->where(array('id'=>$order_id))->find();
		
		$wechatPay = new p\wap_wechat();
		$payment['appid']     = 'wxeaf7f6e2a6843349';  //大众版微信公众号  ysbangs
		$payment['mch_id']    = '1232219302';
		$payment['key']       = 'GjOSHhK48Del8qUsevseqxhcQXuOLiMf';
		$payment['M_OrderNO'] = $orderInfo['sn'];
		$payment['M_Amount']  = '0.01';
		//$payment['notify_url']= __BASE__.UC('Wechat/Share/notifyurl');
		$payment['notify_url']= "http://wxc.ysbang.cn/weixin.php/Wechat/Share/notifyurl";
		
		\Common\Lib\Pclass\Webown::debug_log('传过去的参数==='.print_r($payment,true));
		$sendData = $wechatPay->getSendData($payment);
		
		$wechatPay->doPay($sendData);
		exit;
	}
	
	public function notifyurlAct(){
		
		$data = file_get_contents("php://input");
		\Common\Lib\Pclass\Webown::debug_log('异步通知开始yyyyy==='.print_r($data,true));
		if(!$data) $this->error('非法操作！');
		$wechatPay = new p\wap_wechat();
		$check = $wechatPay->serverCallback($_POST,'GjOSHhK48Del8qUsevseqxhcQXuOLiMf');
		if($check){
			\Common\Lib\Pclass\Webown::debug_log(print_r('验签成功',true));
			$order_id = $_REQUEST['out_trade_no'];
			$checkOrder = D('SpecialEventOrder')->where(array('id'=>$order_id))->find();
			if($checkOrder['status'] == 0){
				$result = D('SpecialEventOrder')->where(array('id'=>$order_id))->save(array('status'=>1));
				if($result){
					\Common\Lib\Pclass\Webown::debug_log(print_r('修改订单状态成功，异步通知结束',true));
					$wechatPay->notifyStop();
				}
			}else{
				\Common\Lib\Pclass\Webown::debug_log(print_r('订单已支付或者取消',true));
			}
		}else{
			\Common\Lib\Pclass\Webown::debug_log(print_r('验证失败',true));
		}
	}
	
	public function userInfoAct(){
		
		$productid = I('productid');
		$eventid = I('eventid');
		
		$openid = session('openid');
		$cond['openid'] = $openid;
		$cond['event_id'] = $eventid;
		$cond['status'] = 0;
		$orderInfo = D('SpecialEventOrder')->where($cond)->find();
		if($orderInfo){
			redirect(UC('Wechat/Share/order').'?existOrderId='.$orderInfo['id']);
		}
		
		$this->assign('productid',$productid);
		$this->assign('eventid',$eventid);
		$this->display();
	}
	
	public function orderDetailAct(){
		echo "<pre>";
		print_r($_REQUEST);
		exit;
	}
	
	public function orderAct(){
		
		$existOrderId = I('get.existOrderId');
		if(!$existOrderId){
			
			$openid     = session('openid');
			$name       = $_REQUEST['name'];    //收货人
			$phone      = $_REQUEST['phone'];       //收货人联系方式
			$eventId    = $_REQUEST['eventId'];     //活动ID
			$productId  = $_REQUEST['productId'];   //产品ID
			
			//查询产品信息
			$productData = D('SpecialEvent')->getProductData($productId);
			
			//订单详情
			$orderItems['product_id']   = $productId;
			$orderItems['amount']       = 1;    //限抢一套
			$orderItems['unit_price']   = $productData['current_price'];  //活动价
			
			//主订单信息
			$order['openid']     = $openid;
			$order['address']    = '暂无';    //收货地址
			$order['name']       = $_REQUEST['name'];  //收货人
			$order['phone']      = $_REQUEST['phone'];    //收货人联系方式
			$order['event_id']   = $_REQUEST['eventId'];    //活动ID
			$order['third_party_pay'] = $orderItems['unit_price'] * $orderItems['amount'];
			$order['total_cost'] = $order['third_party_pay'];
			$order['add_time']   = time();
			$order['status']     = 0;    //订单状态:0 待付款
			$order['sn']         = time().rand(100000,999999);    //订单状态:0 待付款
			$order['channel']    = 6;    //大众版公众号下单
			
			//生成订单(存储订单信息和订单详情)，并返回订单号
			$order_id = D('SpecialEventOrder')->add($order);
			if($order_id){
				$orderItems['order_id'] = $order_id;
				$res = D('SpecialEventOrderItems')->add($orderItems);	//添加订单详情信息
			}
			$order['id']     = $order_id;
			$order['title']  = D('SpecialEventProduct')->where(array('id'=>$productId))->getField('title');
			$order['amount'] = $orderItems['amount'];
		}else{
			$order = D('SpecialEventOrder')->where(array('id'=>$existOrderId))->find();
			$order['amount'] = $order['third_party_pay'];
			$items = D('SpecialEventOrderItems')->where(array('order_id'=>$existOrderId))->find();
			$order['title']  = D('SpecialEventProduct')->where(array('id'=>$items['product_id']))->getField('title');
		}
		
		$this->assign('order',$order);

		$this->display();
	}
	
	
	


	
}
