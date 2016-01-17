<?php
/**
 * 药师帮财付通支付--请求类
 *
 * PHP version 5.5
 *
 * @category	pay
 * @package     Ten
 * @subpackage  Class
 * @copyright   2015 GZSD
 * @version     SVN: $Id: RequestTen.class.php 43 2015-5-5 11:23:59 Cengp $
 */

namespace Common\Lib\Pay\Ten;

class RequestTen{
	
	/** 网关url地址 */
	protected  $gateUrl = '';
	
	/** 密钥 */
	protected $key = '';
	
	/** 请求的参数 */
	protected $parameters = array();
	
	/** debug信息 */
	protected $debugInfo = '';
	
	public function __construct() {
		
	}
	
	public function initparam(){
		
		$this->gateUrl = "https://www.tenpay.com/cgi-bin/v1.0/service_gate.cgi";
		
		$this->parameters['bank_type'] = "DEFAULT";  	  //银行类型，默认为财付通
		
		$this->parameters['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];//客户端IP
		$this->parameters['fee_type'] = 1;  //币种
			
		//系统可选参数
		$this->parameters['sign_type'] = "MD5";       //签名方式，默认为MD5，可选RSA
		$this->parameters['service_version'] = "1.0"; //接口版本号
		$this->parameters['input_charset'] = "utf-8"; //字符集
		$this->parameters['sign_key_index'] = "1";    //密钥序号
		
		//业务可选参数
		$this->parameters['attach'] = "";    //附件数据，原样返回就可以了
		$this->parameters['product_fee'] = "";    //商品费用
		$this->parameters['transport_fee'] = "0";    //物流费用
		$this->parameters['time_start'] = date("YmdHis");    //订单生成时间
		$this->parameters['time_expire'] = "";    //订单失效时间
		$this->parameters['buyer_id'] = "";    //买方财付通帐号
		$this->parameters['goods_tag'] = "";    //商品标记
		$this->parameters['trade_mode'] = "1";    //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
		$this->parameters['transport_desc'] = "";    //物流说明
		$this->parameters['trans_type'] = "1";    //交易类型
		$this->parameters['agentid'] = "";        //平台ID
		$this->parameters['agent_type'] = "";        //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
		$this->parameters['seller_id'] = "";        //卖家的商户号
	}
	
	/**
	 *获取入口地址,不包含参数值
	 */
	public function getGateURL() {
		return $this->gateUrl;
	}
	
	/**
	 *设置入口地址,不包含参数值
	 */
	public function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}
	
	/**
	 *获取密钥
	 */
	public function getKey() {
		return $this->key;
	}
	
	/**
	 *设置密钥
	 */
	public function setKey($key) {
		$this->key = $key;
	}
	
	/**
	 *获取参数值
	 */
	public function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	 *设置参数值
	 */
	public function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	 *获取所有请求的参数
	 *@return array
	 */
	public function getAllParameters() {
		return $this->parameters;
	}
	
	/**
	 *获取带参数的请求URL
	 */
	public function getRequestURL() {
	
		$this->createSign();
	
		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
	
		//去掉最后一个&
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
	
		$requestURL = $this->getGateURL() . "?" . $reqPar;
	
		return $requestURL;
	
	}
	
	/**
	 *获取debug信息
	 */
	public function getDebugInfo() {
		return $this->debugInfo;
	}
	
	/**
	 *重定向到财付通支付
	 */
	public function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}
	
	/**
	 *重定向post到财付通支付
	 */
	public function doPost() {
		$html = '<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<title>财付通即时到帐程序演示</title>
				</head>
				<body>';
		$html .= '<form action="'.$this->getGateUrl().'" name="tensubmit" method="post" target="_blank">';
		$params = $this->getAllParameters();
		foreach($params as $k => $v) {
			$html .=  "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />\n";
		}
		$html .= '<input type="submit" value="财付通支付">
					</form>正在向支付宝提交数据...
					</body>
				  </html>';
		$html .= "<script>document.forms['tensubmit'].submit();</script>";
		
		return $html;
		
		
	}
	
	/**
	 *创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
	 */
	public function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);
	
		//debug信息
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
	
	}
	
	/**
	 *设置debug信息
	 */
	public function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	

}