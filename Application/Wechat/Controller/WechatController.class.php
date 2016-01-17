<?php

namespace Wechat\Controller;
use Think\Controller;

class WechatController extends Controller {

	var $weObj = null;  // 微信基类
	var $wechat_options = array(); // 微信的配置
	var $userInfo = array(); // 用户信息
	var $openid = ''; // 微信用户在这个公众号上身份唯一值 


	private function init(){
			$this->wechat_options['token']     = C('WECHAT_TOKEN');
			$this->wechat_options['appid']     = C('WECHAT_APPID');
			$this->wechat_options['appsecret'] = C('WECHAT_APPSECRET');
			$this->wechat_options['encodingaeskey'] = C('WECHAT_ENCODINGAESKEY');
			$this->wechat_options['welcome'] 	= C('WECHAT_WELCOME');	
	}
	
	public function _empty($name){
		$this->indexAct();
	}

	public function indexAct(){
	    
    	$code  = I('get.code');
    	$state = I('get.state');
		$this->init();

		$options = array(
			'token'=>	$this->wechat_options['token'], //填写你设定的key
			'appid'=>	$this->wechat_options['appid'], //填写高级调用功能的app id
 			'appsecret'=>$this->wechat_options['appsecret'], //填写高级调用功能的密钥
	 	);			
		$this->weObj = new \Wechat\Lib\wechat($options);

        // 微信验证，一般只需要在第一次配置的时候使用，配置token验证
		if ( isset($_GET["echostr"])){
		    $this->weObj->valid();
		    exit();
		}

		//oauth回调处理
		if($code && $state)
		{
		// 重要返回两个值:access_token,openid
			$result = $this->weObj->getOauthAccessToken();
			if($result)
			{
		// 1,取到用户的信息
				$this->openid 	= $result['openid'];
				$this->userInfo = $this->weObj->getOauthUserinfo($result['access_token'],$result['openid']);

		// 2, 绑定用户到原用户库
				$objLogic = D('User','Logic');
				$objLogic->createUserInfo($this->userinfo); // 记录用户到数据库
				$objLogic->bindUser($this->userinfo);
		// 3, 用户自动登陆
				$objLogic->login($this>userinfo);

			}
			header('location: '.urldecode($state));
			exit();
		}

		// 获取微信服务器发过来的信息
		$this->weObj->revType 			= $this->weObj->getRev()->getRevType();
		$this->weObj->revFrom 			= $this->weObj->getRevFrom();
		$this->weObj->revContent 		= $this->weObj->getRevContent();
		$this->weObj->revEvent			= $this->weObj->getRevEvent();
		$this->openid = $this->weObj->revFrom;
		$replyInfo = array(); // 要回复的内容
		$isShowMenu = false; // 默认返回新闻列表后不需要显示菜单

        // 根据不同类型处理回复信息
	    switch($this->weObj->revType) {
	    		case \Wechat\Lib\wechat::MSGTYPE_TEXT:	    		   
                    // 用户输入文本	    		
	    			$key = $this->weObj->revContent;
			    	$objLogic = D('Text','Logic');
					$replyInfo = $objLogic->setTextReply( $key );
	    			break;
	    		case \Wechat\Lib\wechat::MSGTYPE_IMAGE:
                    // 用户上传图片

			    	$objLogic = D('Image','Logic');
					$replyInfo = $objLogic->setImageReply( $this->weObj->revContent );

	    			break;
	    		case \Wechat\Lib\wechat::MSGTYPE_VOICE:
                    // 用户上传音频
			    	$objLogic = D('Voice','Logic');
					$replyInfo = $objLogic->setVoiceReply( $this->weObj->revContent );

	    			break;
	    		case \Wechat\Lib\wechat::MSGTYPE_VIDEO:
                    // 用户上传视频
			    	$objLogic = D('Video','Logic');
					$replyInfo = $objLogic->setVideoReply( $this->weObj->revContent );

	    			break;
	    		case \Wechat\Lib\wechat::MSGTYPE_LOCATION:
                    // 用户上传位置
				    $this->weObj->revGeo	= $this->weObj->getRevGeo();
			    	$objLogic = D('Location','Logic');
					$replyInfo = $objLogic->setLocationReply( $this->weObj->revGeo );

	    			break;

                // 事件类型
	    		case \Wechat\Lib\wechat::MSGTYPE_EVENT:
					switch ($this->weObj->revEvent['event']) {					
						case 'subscribe': // 刚刚关注的
                        // 用户刚关注本微信公众号
                            // 1，先返回自定义的欢迎信息
							$replyInfo = $this->setSubscribeReply();
							
// 2，通过openid 获取用户信息							
							$this->getUserInfo();

// 3, 绑定用户到原用户库
				    		$objLogic = D('User','Logic');
							$objLogic->createUserInfo($this->userinfo); // 记录用户到数据库
							$objLogic->bindUser($this->userinfo);

// 4, 用户自动登陆
							$objLogic->login($this>userinfo);

// 5, 此时要显示自定义菜单
							$isShowMenu = true;

							break;
// 扫描带参数二维码事件:2. 用户已关注时的事件推送
						case 'SCAN': // 取消关注的
// 事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
// 二维码的ticket，可用来换取二维码图片
//							$key 		= $this->revEvent['key'];
//							$ticket 		= $this->revEvent['ticket'];
							$replyInfo = $this->setSubscribeReply();
							break;
						case 'unsubscribe': // 取消关注的
							$replyInfo = $this->setUnSubscribe();
							break;
						case 'CLICK': // 自定义菜单事件
							$replyInfo = $this->setMenuReply();
							break;
						case 'VIEW': // 自定义菜单事件中的网址链接
							$key 		= $this->weObj->revEvent['key'];
			    			if ( !empty($key)){
			    				exit;
		    				}
							break;
						default :
							break;
					}
	    			break;
	    		default:
	    }


        // 如果有返回值，则进行赋值，并返回信息给用户
		if ( !empty($replyInfo))
		{
			switch($replyInfo['type'])
			{
				case 'text':
    				$this->weObj->text($replyInfo['content'])->reply();

					break;
				case 'news':
    				$this->weObj->news($replyInfo['content'])->reply();

					break;
				case 'music':
    				$this->weObj->music($replyInfo['content'])->reply;
					break;
				case 'link':
    				redirect($replyInfo['url']);
					break;
			}
  
            // 如果不需要显示菜单，则本程序到此结束
			if ( !$isShowMenu ){
				exit();
			}
		}

        //获取菜单操作:
		$result = $this->weObj->getMenu();
	 	return;
		
 }
	

    /**
     * 设置新关注用户的回复有两种情况：１，直接关注．２，二维码扫描关注 有　EventKey 与 Ticket
     */
	public function setSubscribeReply()
	{
        // 扫描带参数二维码事件:1. 用户未关注时，进行关注后的事件推送
        // EventKey 事件KEY值，qrscene_为前缀，后面为二维码的参数值
		$key 			= $this->weObj->revEvent['key'];
		$ticket 		= $this->weObj->revEvent['ticket']; // 二维码的ticket，可用来换取二维码图片

		if ( !empty($this->wechat_options['welcome']))
			$content 	= $this->wechat_options['welcome'];	
		else
			$content    = '欢迎关注．请输入关键词，如九九感冒灵．';

		$_return['type'] = 'text';
		$_return['content'] = $content;
		return $_return;		

	}
	
	public function setUnSubscribe()
	{

		$content 			= '谢谢你的到来，再见';
		$_return['type'] 	= 'text';
		$_return['content'] = $content;
		return $_return;	
	}


    // 取到用户的基本信息
	public function getUserInfo()
	{
		if ( empty($this->openid))
			$this->openid = $this->weObj->revFrom;
		if ( empty($this->openid))
			return $_return;

		$info = $this->weObj->getUserInfo($this->openid);
		if ( is_array($info))
			$this->userInfo = $info;
		return $info;
	}

    public function textAct(){
    	$id = I('get.id');
    	
    	$objLogic = D('Text','Logic');
		$replyInfo = $objLogic->get();
    	

    	echo 'text='.$replyInfo;
    }
    
    public function menuAct(){
    	//获取菜单操作:
    	$options = array(
				'token'=>	C('WECHAT_TOKEN'), //填写你设定的key
				'appid'=>	C('WECHAT_APPID'), //填写高级调用功能的app id
				'appsecret'=>C('WECHAT_APPSECRET'), //填写高级调用功能的密钥
		);
    	$this->weObj = new \Wechat\Lib\wechat($options);
    	//设置菜单
    	$newmenu =  array(
    			"button"=>
    			array(
    					array('type'=>'view','name'=>'自助健身','url'=>'http://www.baidu.com'),
    					array('type'=>'view','name'=>'课程预约','url'=>'http://www.baidu.com'),
    			        array('type'=>'click','name'=>'个人中心','key'=>'MENU_KEY_NEWS')
    			)
    	);
    	$this->weObj->createMenu($newmenu);
    }
    

    
}
