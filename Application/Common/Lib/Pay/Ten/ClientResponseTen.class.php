<?php
/**
 * 药师帮财付通支付--后台应答类
 *
 * PHP version 5.5
 *
 * @category	pay
 * @package     Ten
 * @subpackage  Class
 * @copyright   2015 GZSD
 * @version     SVN: $Id: ResponseTen.class.php 43 2015-5-5 11:23:59 Cengp $
 */

namespace Common\Lib\Pay\Ten;

class ClientResponseTen{
	
	/** 密钥 */
	protected $key = "";
	
	/** 应答的参数 */
	protected $parameters = array();
	
	/** debug信息 */
	protected $debugInfo = "";
	
	//原始内容
	protected $content = "";
	
		
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
	
	//设置原始内容，确保PHP环境支持simplexml_load_string以及iconv这两个函数才可以
	//一般PHP5环境下没问题，PHP4需要检测一下环境是否安装了iconv以及simplexml模块
	public function setContent($content) {
		$this->content = $content;
		
		$xml = simplexml_load_string($this->content);
		$encode = $this->getXmlEncode($this->content);
		
		if($xml && $xml->children()) {
			foreach ($xml->children() as $node){
				//有子节点
				if($node->children()) {
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
					
				} else {
					$k = $node->getName();
					$v = (string)$node;
				}
				
				if($encode!="" && $encode != "UTF-8") {
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}
				
				$this->setParameter($k, $v);			
			}
		}	
	}
	
	//设置原始内容
	//解决PHP4老环境下不支持simplexml以及iconv功能的函数
	public function setContent_backup($content) {
		$this->content = $content;
		$encode = $this->getXmlEncode($this->content);
		$xml = new SofeeXmlParser(); 
		$xml->parseFile($this->content); 
		$tree = $xml->getTree(); 
		unset($xml); 
		foreach ($tree['root'] as $key => $value) {
			if($encode!="" && $encode != "UTF-8") {
				$k = mb_convert_encoding($key, $encode, "UTF-8");
				$v = mb_convert_encoding($value[value], $encode, "UTF-8");								
			}
			else 
			{
				$k = $key;
				$v = $value[value];
			}
			$this->setParameter($k, $v);
		}
	}
	
	
	
	//获取原始内容
	public function getContent() {
		return $this->content;
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
	*是否财付通签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
	*true:是
	*false:否
	*/	
	public function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug信息
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;
		
	}
	
	/**
	*获取debug信息
	*/	
	public function getDebugInfo() {
		return $this->debugInfo;
	}
	
	//获取xml编码
	public function getXmlEncode($xml) {
		$ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
		if($ret) {
			return strtoupper ( $arr[1] );
		} else {
			return "";
		}
	}
	
	/**
	*设置debug信息
	*/	
	public function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	
	/**
	 * 是否财付通签名
	 * @param signParameterArray 签名的参数数组
	 * @return boolean
	 */	
	public function _isTenpaySign($signParameterArray) {
	
		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}			
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug信息
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;		
		
	
	}
	

}