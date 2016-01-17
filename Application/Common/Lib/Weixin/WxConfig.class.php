<?php 
/**
 * 微信配置信息类
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

class WxConfig {
	protected static $_username = 'huaxin';
	
	public function __construct($username, $config=array()){
		//$this->_Config = array_merge($this->_Config, $config);
		
		// 注意：微信对接入验证的响应速度，只要一连接数据库，响应就会超时
		// 所以：此处若是验证，直接输出校验码，不再做任何处理
// 		if(!empty($_GET['echostr'])){
// 			die($_GET['echostr']);
// 		}else{
// 			$this->_loadAppSecret();
// 		}
	}
	
	/**
	 * 获取access_token
	 */
	public static function get_token(){
		$cond['username'] = self::$_username;
		if(!self::$_username){
			\Common\Lib\Pclass\Webown::debug_log("Cann't found app setting record for username=".self::$_username);
		}
		$mp = M('WeixinMp')->field('ctime, expires_in, access_token, appid, appsecret')->where($cond)->find();
		if(!$mp){
			\Common\Lib\Pclass\Webown::debug_log("app settings are not full-completed for username=".self::$_username);
		}
		$expires = $mp['ctime'] + $mp['expires_in'];
		if(time() < $expires){
			$access_token = $mp['access_token'];
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$mp['appid']."&secret=".$mp['appsecret'];
			$curl = new Curl();
			$tmpInfo = $curl->get($url);
			$arr = json_decode($tmpInfo, true);
			$access_token = $arr['access_token'];
			$data['ctime'] = time();
			$data['access_token'] = $access_token;
			M('WeixinMp')->where($cond)->save($data);
		}
		return $access_token;
	} 
}