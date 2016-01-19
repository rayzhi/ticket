<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class TicketController extends CommonController {

    /**
     * 魔幻城首页
     */
    public function indexAct(){
        
        $this->display();
        
    }
    
    /**
     * 列表
     */
    public function tickeListAct(){
       
 
        $this->display();
        
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
     * 支付
     */
    public function doPayAct(){
    
        $order_id = I('order_id');
        if(!$order_id) $this->error('参数错误！');
        $orderInfo = D('TicketOrder')->where(array('id'=>$order_id))->find();
        recordLog($orderInfo,'wechatPay');
        if(!$order_id) $this->error('订单不存在！');
        
        $wechatPay = new \Common\Lib\Pay\pay_wap_wechat\wap_wechat();
        $payment['appid']      = C('WECHAT_APPID'); 
        $payment['mch_id']     = C('WECHAT_MCH_ID');
        $payment['key']        = C('WECHAT_PAY_KEY');
        $payment['M_OrderNO']  = $orderInfo['sn'] ? $orderInfo['sn'] : '1453216079927221';
        $payment['M_Amount']   = '0.01';
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
            $order_id = $check['out_trade_no'];
            $checkOrder = D('TicketOrder')->where(array('sn'=>$order_id))->find();
            if($checkOrder['status'] == 0){
                $result = D('TicketOrder')->where(array('sn'=>$order_id))->save(array('status'=>1));
                if($result){
                    recordLog('订单修改状态成功','wechatPay');                   
                    $wechatPay->notifyStop();
                }
            }else{
                recordLog('订单修改状态失败','wechatPay');            
            }
        }else{
            recordLog('验证失败','wechatPay');           
        }
        recordLog('异步通知结束','wechatPay');
    }


}
