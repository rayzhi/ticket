<?php
/**
 * 药师帮后台----后台公用控制器
 *
 * PHP version 5.5
 *
 * @category	qrug
 * @package     Pharmacist
 * @subpackage  config
 * @copyright   2014 GZSD
 * @version     SVN: $Id: class.php 43 2014-12-12 16:23:59 Cengp $
 */

namespace Wechat\Controller;
defined('IN_HEAVEN') or die('Hacking Attempt!');
use Think\Controller;

class CommonController extends Controller {
	
	//构造方法
	public function _initialize() {

		if(ACTION_NAME != 'notifyurl'){
			if (!session('openid')) $this->getOpenid();
		}			
	}
	
	private function getOpenid()
	{
		$options = array(
				'token'=>	C('WECHAT_TOKEN'), //填写你设定的key
				'appid'=>	C('WECHAT_APPID'), //填写高级调用功能的app id
				'appsecret'=>C('WECHAT_APPSECRET'), //填写高级调用功能的密钥
		);
		$this->weObj = new \Wechat\Lib\wechat($options);
		$getOauthAccessToken = $this->weObj->getOauthAccessToken();
		if (!$getOauthAccessToken['openid']) {
			$url = 'http://wxc.ysbang.cn' . __SELF__;
			$getOauthUrl = $this->weObj->getOauthRedirect($url, '', 'snsapi_base');
			redirect($getOauthUrl);
		} else {
			session('openid',$getOauthAccessToken['openid']);
		}
	}
	
	
	
}