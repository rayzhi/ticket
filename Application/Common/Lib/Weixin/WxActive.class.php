<?php 
/**
 * 终端主动下发类
 * 
 * PHP version 5
 * 
 * @category	Plcass
 * @package     Lib
 * @subpackage  Class
 * @version     SVN: $Id: Wxconfig.class.php 10 2015-02-06 16:29:05Z Cengp $
 */
namespace Common\Lib\Weixin;
use \Common\Lib\Pclass\Curl;
use Common\Lib\Pclass\Webown;

class WxActive {
	
	
	/**
	 * 推送文本消息
	 */
	public function push_text($openid, $msg){
		$token = WxConfig::get_token();
		$data = '{
				    "touser":"'.$openid.'",
				    "msgtype":"text",
				    "text":
				    {
				         "content":"'.$msg.'"
				    }
				}';
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token;
		Webown::debug_log('$url======='.$url);
		$curl = new Curl();
		$tmpInfo = $curl->post($url, $data);
		Webown::debug_log('$$tmpInfo======='.$tmpInfo);
	}
	
	/**
	 * 推送图文消息
	 */
	public function push_news($openid, $articles){
		$token = WxConfig::get_token();
		foreach($articles AS $k => $v){
			if($k > 0){
				$arr .= ',';
			}
			$arr .= '{
					 "title":"'.$v['title'].'",
					 "description":"'.$v['description'].'",
					 "url":"'.$v['description'].'",
					 "picurl":"'.$v['picurl'].'"
				 }';
		}
		
		$data = '{
				    "touser":"'.$openid.'",
				    "msgtype":"news",
				    "news":{
				        "articles": ['.$arr.']
				    }
				}';
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token;
		Webown::debug_log('$$articles======='.$data);
		$curl = new Curl();
		$tmpInfo = $curl->post($url, $data);
		Webown::debug_log('$$tmpInfo======='.$tmpInfo);
	}
}