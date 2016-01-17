<?php 
/**
 * 药师帮后台----微信回调
 *
 * PHP version 5.5
 *
 * @category	qrug
 * @package     Pharmacist
 * @subpackage  config
 * @copyright   2014 GZSD
 * @version     SVN: $Id: class.php 43 2014-12-12 16:23:59 Cengp $
 */
namespace Chat\Chat;
use Common\Lib\Weixin as w;
use Common\Lib\Weixin\Wxconfig;

class ReceiveChat extends CommonChat {
	
	public function _empty($name){
		// 使用默认接收处理
		\Common\Lib\Pclass\Webown::debug_log('get==data=='.print_r($_GET));
		if($_GET ["signature"] && $_GET ["timestamp"] && $_GET ["nonce"]){
			$config = new w\WxConfig($this->_username);
			$receive = new w\WxReceive($config);
			$receive->run();
			
			if($_GET["echostr"]) $wechatObj->valid(self::VERIFY_TOKEN);
			else $wechatObj->responseMsg();
		}
		
	}
}