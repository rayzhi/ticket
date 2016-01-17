<?php 
/**
 * 药师帮公用授权类
 * 
 * PHP version 5
 * 
 * @category	Pharmacist
 * @package     Admin
 * @subpackage  Auth
 * @version     SVN: $Id: WebAuth.class.php 10 2014-12-12 15:34:05Z cengp $
 */
namespace Admin\Auth;

class AdminAuth extends WebAuth{
	
	protected $_loginPath = '/admin/public/login';
	
	/**
	 * 判断是否登陆
	 *
	 * @return bool 已经登陆返回true, 尚未登陆返回false
	 */
	public function isLogin()
	{
		if ($this->isEmpty('uid')) return false;
	
		$uid = $this->getUid();
		$loginTime = $this->getLoginTime();
		$loginIp   = $this->getLoginIp();
		$signin = $this->read('signin');
		if ($signin && $uid
		&& !strcmp($signin, crc32($uid.$loginTime.$loginIp))
		)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 跳转到登入页面
	 *
	 * 注：本函数会立即退出程序执行
	 *
	 * @param string $redirect 回跳链接
	 */
	public function redirectLogin($tips=array(), $redirect=null)
	{
		if(!$redirect){
			$redirect = UC('Admin/' . CONTROLLER_NAME . '/' . ACTION_NAME);
		}
		
		$path = parse_url($redirect, PHP_URL_QUERY);
		$pathPattern = '@^' . preg_quote($this->_loginPath, '@') . '@gi';
		if(preg_match($pathPattern, $path)){
			$redirect = __BASE__;
		}
		
		$goto = UC('admin/public/login');
		if($redirect){
			$goto = url_append_param($goto, array(C('VAR_REDIRECT')=>$redirect));
		}
		if(is_string($tips)){
			$tips = array('msg'=>$tips);
		}
		if($tips){
			$goto = url_append_param($goto, $tips);
		}
		redirect($goto);
	}
	
	/**
	 * 写入登入session
	 *
	 * @param string $uid uid
	 * @param string $uname 登入名
	 */
	public function login($uid, $user=NULL)
	{
		$sin_time = time();
		$sin_ip = get_client_ip();
	
		$array = array(
				'platform'        => 'youanbao',       
				'uid' 			  => $uid,
				'user'            => $user,
				'signin'	      => crc32($uid.$sin_time.$sin_ip),
				'loginTime'       => $sin_time,
				'loginIp'         => $sin_ip,
		);
		
		$this->write($array);
	}
	
	/**
	 * 销毁登入session
	 *
	 * @return void
	 * @access public
	 */
	public function logout()
	{
		$this->destroy();
	}
	
	public function getUid(){return $this->read('uid');}
	public function getUser($field=NULL){
		$userInfo = $this->read('user');
		return !$field?$userInfo:$userInfo[$field];
	}
	public function getLoginTime(){return $this->read('loginTime');}
	public function getLoginIp(){return $this->read('loginIp');}
	
	public function checkAccess($type){
		$allow = false;
		$userInfo = $this->getUser();
		$allow = $type && !empty($userInfo) && (in_array('all', $userInfo['permissions']) || in_array($type, $userInfo['permissions']));
		return $allow;
	}
	
	public function isSuper(){
		return !strcasecmp($this->getUser('type'), 'super');
	}
	
	public function isAdmin(){
		return !strcasecmp($this->getUser('type'), 'admin');
	}
}