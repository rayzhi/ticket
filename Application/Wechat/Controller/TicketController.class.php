<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class TicketController extends CommonController {
    
    protected $payType = PAYTYPE;  //支付方式----微信支付

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
        foreach($areainfo['data'] as $k=>$v){
            $venuesinfo = R('Api/venuesinfo',array($v['id']));
            foreach($venuesinfo['data'] as $k1=>$v1){
                $areaName[$v1['id']]['id'] = $v1['id']; 
                $areaName[$v1['id']]['name'] = $v['name'].$v1['name'];
            }
        }
        $this->assign('activity_text',getSysConfig('activity-text'));
        $this->assign('venuesinfo',$areaName);
       
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
            $back['total'] = sprintf('%.2f',$price * $amount);
            $back['price'] = sprintf('%.2f',$price);
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
        if($orderInfo['coupon_pay'] == 0){
            $couponInfo = \Wechat\Logic\CouponLogic::getAllCoupon($openid);
            $this->assign('couponInfo',$couponInfo);
            
            if($couponInfo && $orderInfo['third_party_pay'] > $couponInfo[0]['price']){
                $orderInfo['third_party_pay'] = $orderInfo['third_party_pay'] - $couponInfo[0]['price'];            
            }elseif($couponInfo && $orderInfo['third_party_pay'] <= $couponInfo[0]['price']){
                $orderInfo['third_party_pay'] = 0;
            }
        }
        
        $this->assign('orderInfo',$orderInfo);
        
        $this->display();
    }
    
    public function changeCouponAct(){
        
        $coupon_id = I('coupon_id');
        if($coupon_id != 0){
            $tmp = explode("-", $coupon_id);
            $couponId = $tmp[0];
            $couponType = $tmp[1];
        }
        if(!$couponType){
            $couponType = 0;
        }
        
        $oid = I('oid');
        
        $price = \Wechat\Logic\CouponLogic::getCouponPrice($couponId,$couponType);
        $order = D('TicketOrder')->where(array('id'=>$oid))->find();
        
        $p = $order['third_party_pay'] - $price;
        $p = $p > 0 ? $p : 0;
        $this->success($p);
        
    }
    
    /**
     * 支付
     */
    public function doPayAct(){
    
        $order_id = I('oid');
        $coupon_id = I('cid');
        if($coupon_id != 0){
            $tmp = explode("-", $coupon_id);
            $coupon_id = $tmp[0];
            $couponType = $tmp[1];
        }
        if(!$order_id) $this->error('参数错误！');
        if($coupon_id){
            $useResult = D('UserCoupon')->useCoupon($order_id,$coupon_id,$couponType);//使用优惠券
        }
        $orderInfo = D('TicketOrder')->where(array('id'=>$order_id))->find();
        if($orderInfo['third_party_pay'] == 0){
            $this->enoughPayAct($orderInfo);
            exit;
        }
        
        if(!$order_id) $this->error('订单不存在！');
        $wechatPay = new \Common\Lib\Pay\pay_wap_wechat\wap_wechat();
        $payment['appid']      = C('WECHAT_APPID'); 
        $payment['mch_id']     = C('WECHAT_MCH_ID');
        $payment['key']        = C('WECHAT_PAY_KEY');
        $payment['M_OrderNO']  = $orderInfo['sn'];
        $payment['M_Amount']   = $orderInfo['third_party_pay'];
//        $payment['M_Amount']   = 0.01;
        $payment['notify_url'] = __BASE__.UC('Wechat/Ticket/notifyurl');

        recordLog($payment,'wechatPay');        
        $sendData = $wechatPay->getSendData($payment);
        header('Content-Type:text/html;charset=utf-8');
        $wechatPay->doPay($sendData);
        exit;
        
    }
    
    public function enoughPayAct($orderInfo){
        
        $save['status'] = 1;
        $save['third_pay_id'] = $this->payType;//不用支付
        $result = D('TicketOrder')->where(array('sn'=>$orderInfo['sn']))->save($save);
        recordLog('优惠券足够支付','wechatPay');
        recordLog($orderInfo,'wechatPay');
        //调用取票sn接口
        $this->addTicketSn($orderInfo['sn']);
        redirect(__BASE__.UC('Wechat/User/orderlist'));
        
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
                    //调用取票sn接口
                    $this->addTicketSn($order_sn);
                    recordLog('订单修改状态成功','wechatPay');
                    //赠送支付人的推荐人优惠券
                    \Wechat\Logic\CouponLogic::givePayInvertCoupon($checkOrder['open_id']);            
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
