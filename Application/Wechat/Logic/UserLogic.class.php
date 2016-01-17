<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class TextReplyLogic extends Model{

	Protected $autoCheckFields = false;

	public function get(){
		return 'test';
	}

/*
记录用户的动作
放到表 ts_wechat_userlog 中
主要是记录用户进入公众号，点击菜单等
跟消息管理不同的是，这里专注于用户行为的记录。
以方便后面对用户进行更深层次的分析

hao	2015-05-10 16:34
	*/
	public function addUserLog()
	{
		$_return = 0;

		if (empty($this->openid))
			$this->openid = $this->weObj->revFrom;			
		if ( empty($this->openid))
			return $_return;
// 如果不是动作则不记录，如用户发消息这个值是为空的
//	    if( empty($this->weObj->revEvent['event']) )
//			return $_return;

		$data['type']			= strtolower($this->weObj->revEvent['event']);

		$data['openid'] 		= $this->openid;
		$data['appid']			= $this->wechat_options['appid'];
		$data['ctime']			= time();

		$content['openid'] 		= $this->openid;
		$content['type'] 		= $this->weObj->revType;
		$content['event'] 		= $data['type'];
		$content['key'] 		= $this->weObj->revEvent['key'];
		$content['time']      	= ITime::getDateTime();

		$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		if( !empty($http_user_agent))
		{
			$http_user_agent = str_replace('/',' ',$http_user_agent);
			$http_user_agent = str_replace('(',' ',$http_user_agent);
			$http_user_agent = str_replace(')',' ',$http_user_agent);
//			$content['http_user_agent'] = $http_user_agent;
		}

		$data['content']		= json_encode($content,'utf8');

		$logObj = new IModel('wechat_userlog');
		$logObj->setData($data);
		$log_id = $logObj->add();

		return $log_id;
	}



/*
保存用户信息
每次用户进入公众号，尽可能把用户的信息保存到本地数据库当中
然后与会员绑定，拿出更多用户信息
hao 2014-04-24  12:39
*/
	public function saveUserInfo()
	{
		return $this->createUserInfo();
	}
/*
用户刚关注公众号时，抓取用户的基本信息，放入数据库

hao  2015-05-11 00:39
*/
	public function createUserInfo()
	{
		$_return = 0;
		
		if ( empty($this->openid))
			$this->openid = $this->weObj->revFrom;
		if ( empty($this->openid))
			return $_return;
// 如果不是空（刚进入公众号）或是关注，则退出
// 一般只需要记录一次即可



		$modelWechatUsers = new IModel('wechat_users');
		$info = $modelWechatUsers->getObj('openid="'.$this->openid.'"');

		$data['sex']				= $this->userInfo['sex'];
		$data['country']			= $this->userInfo['country'];
		$data['province']			= $this->userInfo['province'];
		$data['city']				= $this->userInfo['city'];
		$data['language']			= $this->userInfo['language'];
		$data['nickname']			= $this->userInfo['nickname'];
		$data['headimgurl']			= $this->userInfo['headimgurl'];
		$data['subscribe_time']		= $this->userInfo['subscribe_time'];

		$data['ctime']				= time();
		$data['content']			= '';
		$data['uid']				= 0;
	
		if ( empty($info))
		{
			$data['openid']			= $this->openid;
			$modelWechatUsers->setData($data);
			$_return = $modelWechatUsers->add();
		}else{
			$where = 'id='.$info['id'];
			$modelWechatUsers->setData($data);
			$_return = $modelWechatUsers->update($where);
		}
		
		return $_return;	
	}


	/**
	 * @brief 绑定微信账号到用户系统
	 * @param array $userData array(unionid,headimgurl,sex,nickname,openid)
	 */
	public function bindUser()
	{
		$_return = 0;

		if ( empty($this->openid))
			$this->openid = $this->weObj->revFrom;
		if ( empty($this->openid))
			return $_return;

		//保存openid为其他wechat应用使用
		ISafe::set('wechat_openid',$this->openid);

		$oauthUserDB = new IModel('oauth_user');
		$oauthRow = $oauthUserDB->getObj("oauth_user_id = '".$this->openid."' and oauth_id = 4");

		//用户数据不存在
		if(!$oauthRow)
		{
			$userDB    	= new IModel('user');
			$username 	= substr($this->openid,0,20);
			//插入user表
			$userDB->setData(array(
				'username' => $username,
				'password' => md5(time()),
			));
			$user_id = $userDB->add();

			$area = $this->userInfo['country'].$this->userInfo['province'].$this->userInfo['city'];

			//插入member表
			$memberDB = new IModel('member');
			$memberDB->setData(array(
				'user_id' 		=> $user_id,
				'sex'     		=> $this->userInfo['sex'],
				'true_name'     => $this->userInfo['nickname'],
				'area'     		=> $area,
				'vip_right'		=> 3,
				'time'    		=> ITime::getDateTime(),
			));
			$memberDB->add();

			//插入oauth_user关系表
			$oauthUserData = array(
				'oauth_user_id' => $this->openid,
				'oauth_id'      => 4,
				'user_id'       => $user_id,
				'datetime'      => ITime::getDateTime(),
			);
			$oauthUserDB->setData($oauthUserData);
			$oauthUserDB->add();
		}
	}

	/**
	 * @brief 登录用户系统
	 * @param array $userData array(unionid,headimgurl,sex,nickname,openid)
	 */
	public function login()
	{
		$_return = 0;

		if ( empty($this->openid))
			$this->openid = $this->weObj->revFrom;
		if ( empty($this->openid))
			return $_return;

		$oauthUserDB = new IModel('oauth_user');
		$oauthRow = $oauthUserDB->getObj("oauth_user_id = '".$this->openid."' and oauth_id = 4");

		$userDB = new IModel('user');
		$userRow = $userDB->getObj('id = '.$oauthRow['user_id']);

		//用户私密数据
		ISafe::set('user_id',$userRow['id']);
		ISafe::set('username',$userRow['username']);
		ISafe::set('head_ico',$userRow['head_ico']);
		ISafe::set('user_pwd',$userRow['password']);
		ISafe::set('last_login','');
		ISafe::set('openid',$this->openid);
		ICookie::set('openid',$this->openid);

		return;

		$data['type']			= 'login_log';
		$data['openid'] 		= $this->openid;
		$data['appid']			= $this->wechat_options['appid'];
		$data['ctime']			= time();
		$content['openid'] 		= $this->openid;
		$content['user_id'] 	= $userRow['id'];
		$content['username'] 	= $userRow['username'];
		$content['password'] 	= $userRow['password'];

		$data['content']		= json_encode($content);

		$logObj = new IModel('wechat_userlog');
		$logObj->setData($data);
		$log_id = $logObj->add();


		if(!$userRow)
		{
			die('无法获取微信用户与商城的绑定信息');
		}

		CheckRights::loginAfter($userRow);
	}


}
