<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class TicketController extends CommonController {
	
	var $payType = 4; //微信支付

    /**
     * 魔幻城首页
     */
    public function indexAct(){
    	
        $userinfo = \Wechat\Logic\UserLogic::getUserinfo(getOpenid());
        $this->assign('userinfo',$userinfo);
        $this->assign('activity_text',getSysConfig('activity-text'));
        $this->display();
    }
    
    /**
     * 列表
     */
    public function tickeListAct(){
        
        $areainfo = R('Api/areainfo');
       
        //测试时候地区暂时取----佛山
        $venues_id = $areainfo['data'][2]['id'];
        $areaName = $areainfo['data'][2]['name'];
        $this->assign('areaName',$areaName);
   
        //该地区的场馆
        $venuesinfo = R('Api/venuesinfo',array($venues_id));
        $this->assign('venuesinfo',$venuesinfo['data']);
       
        //单票的信息4--微信支付   场馆id 2--成人票
//         $ticketInfo = R('Api/queryprice',array($this->payType,$venues_id));
//         foreach($ticketInfo['data'] as $k=>$v){
//            $ticketType[$v['ticketTypeId']]['id'] = $v['ticketTypeId'];
//            $ticketType[$v['ticketTypeId']]['name'] = $v['ticketType'];
//            $ticketType[$v['ticketTypeId']]['ticketMainType'] = 0;
//         }
        
//         //套票
//         $taoPiao = R('Api/price',array($venues_id,$this->payType));
//         foreach($taoPiao['data'] as $k=>$v){
//            $ticketType1[$v['id']]['id'] = $v['id'];
//            $ticketType1[$v['id']]['name'] = $v['name'];
//            $ticketType1[$v['id']]['ticketMainType'] = 1;
//         }
        
        $this->assign('activity_text',getSysConfig('activity-text'));
        //$this->assign('ticketType',$ticketType);
        //$this->assign('ticketType1',$ticketType1);
        //$this->assign('ticketInfo',$ticketInfo['data'][0]);

        $this->display(); 
        
    }
    
    public function getVenuesAct(){
    	
    	if(IS_POST){
    		
    		$venues_id = I('venues_id');
    		if($venues_id){
    		
		    	//单票的信息4--微信支付   场馆id 2--成人票
		    	$ticketInfo = R('Api/queryprice',array($this->payType,$venues_id));
		    	foreach($ticketInfo['data'] as $k=>$v){
		    		$ticketType[$v['ticketTypeId']]['id'] = $v['ticketTypeId'];
		    		$ticketType[$v['ticketTypeId']]['name'] = $v['ticketType'];
		    		$ticketType[$v['ticketTypeId']]['ticketMainType'] = 0;
		    	}
		    	
		    	//套票
		    	$taoPiao = R('Api/price',array($venues_id,$this->payType));
		    	foreach($taoPiao['data'] as $k=>$v){
		    		$ticketType1[$v['id']]['id'] = $v['id'];
		    		$ticketType1[$v['id']]['name'] = $v['name'];
		    		$ticketType1[$v['id']]['ticketMainType'] = 1;
		    	}
    		}
	    	
	    	$this->assign('ticketType',$ticketType);
	    	$this->assign('ticketType1',$ticketType1);
	    	
	    	$this->success($this->fetch('Ticket:venuesLib'));
    	}
    	
    }
    
    
    public function getTicketPriceAct(){
        
        if(IS_POST){       
            if($_POST['main_type'] == 1){
                $ticketInfo = R('Api/price',array($_POST['venues_id'],$this->payType,$_POST['ticket_type_id']));
                $price = 0;
                foreach($ticketInfo['data'][0]['tickets'] as $k=>$v){
                    $price += $v['price'] * $v['count'];
                }               
            }else{
                $ticketInfo = R('Api/queryprice',array($this->payType,$_POST['venues_id'],$_POST['ticket_type_id']));
                $price = $ticketInfo['data'][0]['price'];
            }
            $amount = I('amount') ? I('amount') : 1;
            $price = $price ? $price : 0;
            $back['total'] = $price * $amount;
            $back['price'] = $price;
            $this->success('成功',$back);
        }
    }
    
    /**
     * 下单
     */
    public function makeOrderAct(){
        
        if(IS_POST){
            $order_id = D('TicketOrder')->makeOrder($_POST);
            if($order_id){
                $this->success('下单成功!',$order_id);
            }else{
                $this->error('下单失败!');
            }
        }
        
    }
    
    /**
     * 确认订单
     */
    public function sureOrderAct(){
        
        $openid   = session('openid');
        $order_id = I('order_id');
        if(!$order_id) $this->error('参数错误！');
        $orderInfo = D('TicketOrder')->getOrderInfo($order_id);
        $couponInfo = D('UserCoupon')->getMaxCoupon($openid);
 
        if($couponInfo && $orderInfo['third_party_pay'] > $couponInfo['price']){
            $orderInfo['third_party_pay'] = $orderInfo['third_party_pay'] - $couponInfo['price'];            
        }else{
            $couponInfo['id'] = $couponInfo['price'] = 0;
        }
        
        $this->assign('orderInfo',$orderInfo);
        $this->assign('couponInfo',$couponInfo);
        
        $this->display();
    }
    
    /**
     * 支付
     */
    public function doPayAct(){
    
        $order_id = I('oid');
        $coupon_id = I('cid');
        if(!$order_id) $this->error('参数错误！');
        if($coupon_id){
            $useResult = D('UserCoupon')->useCoupon($order_id,$coupon_id);//使用优惠券
            if($useResult['code'] == 2) $this->error($useResult['msg']);
        }
        $orderInfo = D('TicketOrder')->where(array('id'=>$order_id))->find();
        if(!$order_id) $this->error('订单不存在！');
        $wechatPay = new \Common\Lib\Pay\pay_wap_wechat\wap_wechat();
        $payment['appid']      = C('WECHAT_APPID'); 
        $payment['mch_id']     = C('WECHAT_MCH_ID');
        $payment['key']        = C('WECHAT_PAY_KEY');
        $payment['M_OrderNO']  = $orderInfo['sn'];
        $payment['M_Amount']   = '0.01';//测试金额
        $payment['notify_url'] = __BASE__.UC('Wechat/Ticket/notifyurl');

        recordLog($payment,'wechatPay');        
        $sendData = $wechatPay->getSendData($payment);
        header('Content-Type:text/html;charset=utf-8');
        $wechatPay->doPay($sendData);
        exit;
        
    }
    
    public function notifyurlAct(){
        
        recordLog('异步通知开始','wechatPay');
        $wechatPay    = new \Common\Lib\Pay\pay_wap_wechat\wap_wechat();
        $check = $wechatPay->serverCallback(C('WECHAT_PAY_KEY'));
        if($check){           
            recordLog('验证成功','wechatPay');
            recordLog($check,'wechatPay');
            $order_sn = $check['out_trade_no'];
            $checkOrder = D('TicketOrder')->where(array('sn'=>$order_sn))->find();
            if($checkOrder['status'] == 0){
                $save['status'] = 1;
                $save['third_pay_id'] = $this->payType;
                $result = D('TicketOrder')->where(array('sn'=>$order_sn))->save($save);              
                if($result){
                    //赠送支付人的推荐人优惠券
                    \Wechat\Logic\CouponLogic::givePayInvertCoupon($checkOrder['open_id']);
                    //调用取票sn接口
                    $this->addTicketSn($order_sn);
                    recordLog('订单修改状态成功','wechatPay');                   
                    $wechatPay->notifyStop();
                }
            }else{
                recordLog('订单已经支付或取消','wechatPay');            
            }
        }else{
            recordLog('验证失败','wechatPay');           
        }
        recordLog('异步通知结束','wechatPay');
    }
    
    /**
     * 支付成功，获取票序列号入库
     * @param string $order_sn
     */
    private function addTicketSn($order_sn){
        
        recordLog('调取weixinbuy接口开始','wechatPay');
        $orderInfo = D('TicketOrder')->orderAllInfo($order_sn);
        recordLog($orderInfo,'wechatPay');
        $result = R('Api/weixinbuy',array($orderInfo));
        if($result['data']){
            recordLog($result['data'],'wechatPay');
            foreach($result['data'] as $k=>$v){
                if($v['ticketNo']){
                    D('TicketSn')->addTicketSn($orderInfo['did'],$v['ticketNo'],createQr($v['ticketNo']),$v['expiryDate'],$v['price'],$v['ticketTypeId'],$v['ticketTypeName']);                   
                }
            }
            $snResult = D('TicketOrder')->ticketPriceUseCoupon($order_sn);
            $this->returnPrice($snResult);//返回价格
        }else{
            recordLog('返回票sn失败','wechatPay');
        }
        recordLog('调取weixinbuy接口结束','wechatPay');
        return true;
        
    }
    
    /**
     * 回传价格
     * @param unknown $snResult
     */
    private function returnPrice($snResult){
    	
    	if($snResult){
    		$count = count($snResult);
    		$coupon = $snResult[0]['total_cost'] - $snResult[0]['third_party_pay'];
    		$i = 0;
    		while($coupon > 0 && $i <= $count){
    			$coupon = $snResult[$i]['t_price'] - $coupon;
    			if($coupon >= 0){
    				$coupon = 0;
    				$snResult[$i]['t_price'] = $coupon;
    			}else{
    				$coupon = abs($coupon);
    				$i++;
    			}
    		}
    		foreach($snResult as $k=>$v){
    			R('Api/wxcallback',array($v['ticket_sn'],$v['t_price']));//返回票的价格
    		}
    	}
    	
    }


}
