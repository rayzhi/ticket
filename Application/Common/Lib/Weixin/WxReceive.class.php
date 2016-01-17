<?php 
/**
 * 接收微信调用类
 * 
 * PHP version 5
 * 
 * @category	Plcass
 * @package     Lib
 * @subpackage  Class
 * @version     SVN: $Id: Wxconfig.class.php 10 2015-02-06 16:29:05Z Cengp $
 */
namespace Common\Lib\Weixin;

class WxReceive extends WeChat {
	
	
	/**
     * 分析消息类型，并分发给对应的函数
     *
     * @return void
     */
	public function run($responseObj=NULL){
		$request_url = self::get_request_url();
		$client_ip = self::get_client_ip();
	
		$this->_log("Received new request from {$client_ip}");
		$this->_log("Request URL: {$request_url}");
		$this->_log("Get: " . print_r($_GET, true));
		$this->_log("Post: " . print_r($_POST, true));
	
		switch ($this->getRequest('msgtype')) {
		
			case 'event':
				switch ($this->getRequest('event')) {
					
					case 'subscribe':
						$this->onSubscribe();
						break;
		
					case 'unsubscribe':
						$this->onUnsubscribe();
						break;
		
					case 'SCAN':
						$this->onScan();
						break;
		
					case 'LOCATION':
						$this->onEventLocation();
						break;
		
					case 'CLICK':
						$this->onClick();
						break;
		
				}
		
				break;
		
			case 'text':
				$this->onText();
				break;
		
			case 'image':
				$this->onImage();
				break;
		
			case 'location':
				$this->onLocation();
				break;
		
			case 'link':
				$this->onLink();
				break;
		
			case 'voice':
				$this->onVoice();
				break;
		
			default:
				$this->onUnknown();
				break;
		
		}
	}
	
	
}