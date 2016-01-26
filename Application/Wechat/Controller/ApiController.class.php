<?php

/**
 * 票务系统--接口模块
 * 
 */
namespace Wechat\Controller;
use \Common\Lib\Pclass as t;

class ApiController extends CommonController {
    
    protected $apiurl = 'http://120.76.43.54:8082';//接口的基本域名

    /**
     * 查询票价
     * @param $payType     1 现金支付  2 美团  3 ...
     * @param $venuesId    场馆id
     * @param $ticketType  1 儿童票     2 成人票
     * 
     */
    public function querypriceAct($payType,$venuesId,$ticketType=null){
   
        $token  = $this->getToken(md5($payType.$venuesId));
        $url    = $this->apiurl.'/sys/buytype/queryprice?token='.$token;
        $param  = '&payType='.$payType.'&venuesId='.$venuesId;
        if($ticketType) $param .= '&ticketType='.$ticketType;
        $url    = $url.$param;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        
        return $return;
        
    }
    
    /**
     * 买票
     */
    public function weixinbuyAct($orderInfo){
        
        $token  = $this->getToken(md5($orderInfo['third_pay_id'].$orderInfo['venues_id'].$orderInfo['ticket_type']));
        $url    = $this->apiurl.'/sys/ticket/weixinbuy?token='.$token;

        $param = array(
            'ticketMainType' => $orderInfo['ticket_main_type'], //票主要类型，1：套票；0：普通类型票-单票
            'venuesId'       => $orderInfo['venues_id'],
            'payType'        => $orderInfo['third_pay_id'],
            'ticketType'     => $orderInfo['ticket_type'],
            'orderNo'        => $orderInfo['sn'],   //订单号
            'weixinName'     => $orderInfo['nickname'], //微信昵称
            'weixinNo'       => $orderInfo['open_id'],//微信号 
            'userSex'        => $orderInfo['sex'], //性别1女 0男
            'count'          => $orderInfo['amount'], //票数
            'expiryDate'     => date('Y-m-d H:i:s',$orderInfo['expiry_date'])  //过期时间（2016-02-10 00:00:00）
        );
       
        foreach($param as $k=>$v){
            $url .= '&'.$k.'='.urlencode($v);
        }

        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
 
        return $return;
    
    }
    
    /**
     * 套票信息
     */
    public function priceAct($venuesId,$payType,$id=null){
    
        $token  = $this->getToken(md5($venuesId));
        $url    = $this->apiurl.'/sys/ticketset/price?token='.$token;
        $param  = '&payType='.$payType.'&venuesId='.$venuesId;
        if($id) $param .= '&id='.$id;
        $url    = $url.$param;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        return $return;
    
    }
    
    /**
     * 地区列表获取
     * $areainfo = R('Api/areainfo');
     * 
     */
    public function areainfoAct(){
        
        $token  = $this->getToken(md5('areainfo'));
        $url    = $this->apiurl.'/sys/areainfo/select?token='.$token;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
   
        return $return;
    
    }
    
    /**
     * 场馆列表获取
     * @param $c_areaId 地区id
     * $venuesinfo = R('Api/venuesinfo',array(1));
     * 
     */
    public function venuesinfoAct($c_areaId){
        
        $token  = $this->getToken(md5('venuesinfo'));
        $url    = $this->apiurl.'/sys/venuesinfo/select?token='.$token.'&c_areaId='.$c_areaId;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);

        return $return;
    
    }
    
    /**
     * 票实际销售价格回传
     * post方式
     */
    public function wxcallbackAct($ticketNo,$price){
    
         $param = array(
             'ticketNo' => $ticketNo,
             'price'    => $price
        );
        $token    = $this->getToken(md5($ticketNo.$price));
        $data[]   = $param;
      
        $url      = $this->apiurl.'/sys/ticketsaleinfo/wxcallback?token='.$token;
        $curlData = json_encode($data);
        $curl     = new t\Curl();
        $return   = $curl->post($url,$curlData,1);
       
        return $return;
    
    }
    
    /**
     * 查询票的状态
     * @param string $ticket_sn 票的序列号  可传单个或者多个   多个用英文逗号隔开   
     */
    public function ticket_use_timeAct($ticket_sn){
    	
    	$token  = $this->getToken(md5('ticketusetime'));
    	$url    = $this->apiurl.'/sys/ticket/ticket_use_time?token='.$token.'&ticketNo='.$ticket_sn;
    	echo $url;exit;
    	$curl   = new t\Curl();
    	$return = $curl->get($url);
    	$return = json_decode($return,true);
    	return $return;
    	exit;
    	
    	$param = array(
    			'ticketNo' => $ticket_sn
    	);
    	$token    = $this->getToken(md5('ticketusetime'));
    	$data[]   = $param;
    
    	$url      = $this->apiurl.'/sys/ticket/ticket_use_time?token='.$token;
    	$curlData = json_encode($data);
    	$curl     = new t\Curl();
    	$return   = $curl->post($url,$curlData,1);
    	 var_dump($return);
    	return $return;
    	
    }
    
    
    /**
     * 计算token的方式
     * @param string md5($aString + b)  
     * 
     */
    private function getToken($aString){
        
        return md5($aString.md5(date('Y-m-d')));
        
    }
    
    

}
