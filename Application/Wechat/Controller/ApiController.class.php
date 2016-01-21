<?php

/**
 * 票务系统--接口模块
 * 
 */
namespace Wechat\Controller;
use \Common\Lib\Pclass as t;

class ApiController extends CommonController {
    
    protected $apiurl = 'http://test.ysbang.cn';//接口的基本域名

    /**
     * 查询票价
     */
    public function queryprice($payType,$venuesId,$ticketType){
        
        $token  = $this->getToken(md5($payType.$venuesId.$ticketType));
        $url    = $this->apiurl.'/sys/buytype/queryprice?token='.$token;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        return $return;
        
    }
    
    /**
     * 买票
     */
    public function weixinbuy($payType,$venuesId,$ticketType){
    
        $token  = $this->getToken(md5($payType.$venuesId.$ticketType));
        $url    = $this->apiurl.'/sys/ticket/weixinbuy?token='.$token;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        return $return;
    
    }
    
    /**
     * 套票信息
     */
    public function price($venuesId,$payType){
    
        $token  = $this->getToken(md5($venuesId));
        $url    = $this->apiurl.'/sys/ticketset/price?token='.$token;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        return $return;
    
    }
    
    /**
     * 地区列表获取
     */
    public function areainfo(){
    
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
     */
    public function venuesinfo($c_areaId){
    
        $token  = $this->getToken(md5($c_areaId));
        $url    = $this->apiurl.'/sys/venuesinfo/select?token='.$token;
        $curl   = new t\Curl();
        $return = $curl->get($url);
        $return = json_decode($return,true);
        return $return;
    
    }
    
    /**
     * 票实际销售价格回传
     */
    public function wxcallback($ticketNo,$price){
    
        $arr = array(
            'ticketNo' => $ticketNo,
            'price'    => $price
        );
        $token    = $this->getToken(md5($ticketNo.$price));
        $url      = $this->apiurl.'/sys/ticketsaleinfo/wxcallback?token='.$token;
        $curlData = json_encode($arr);
        $curl     = new t\Curl();
        $return   = $curl->get($url);
        $return   = json_decode($return,true);
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
