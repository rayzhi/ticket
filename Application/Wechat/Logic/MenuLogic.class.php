<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class TextReplyLogic extends Model{

	public function get(){
		return 'test';
	}

	/**
	 * @brief 获取自定义菜单
	 * @return array
	 */
/*
以下是新程序。
注意是从json配置文件取数据
并且转换为静默的网址
hao 2015.05.10 12:23
*/
	public function getMenu()
	{
		$menuData = '';
	  $menuFile = IWeb::$app->getBasePath().'/config/wechat_menu.json';
		if ( file_exists($menuFile))
		{
			$fileObj   = new IFile($menuFile,'r');
			$menuData = $fileObj->read();

// print_r($menuData);

			$menuData = $this->converOauthUrl($menuData);

		}
		return $menuData;
	}



	/**
	 * @brief 更新菜单
	 * @param json $menuData 菜单数据 {"button":[{"name":"名称","sub_button":[{"type":"view","name":"子菜单名称","url":"http://www.aircheng.com"}]}]}
	 * @return array("errcode" => 0,"errmsg" => "ok")
	 */
	public function setMenu($menuData)
	{
		//URL静默登录替换处理
		$menuData = $this->converOauthUrl($menuData);



		$classFileBase = IWeb::$app->getBasePath().'plugins/wechat/wechat_base.php';
// wechat 基类，强大的第三方类库
		if(is_file($classFileBase))
		{
			include_once($classFileBase);

		    $options = array(
					'token'=>	$this->wechat_options['token'], //填写你设定的key
					'appid'=>	$this->wechat_options['appid'], //填写高级调用功能的app id
		 			'appsecret'=>$this->wechat_options['appsecret'], //填写高级调用功能的密钥
		 	);

			$this->weObj = new wechat_base($options);
		}

		$this->weObj->deleteMenu();
		$_return = $this->weObj->createMenu($menuData);
		return $_return ;
	}

	//URL静默登录替换处理
	private function converOauthUrl($menuData)
	{
		$menuArray = json_decode($menuData);
		if(!isset($menuArray['button']))
		{
			return null;
		}

		foreach($menuArray['button'] as $key => $val)
		{
			if($val['type'] == 'view' )
			{
				$url = $this->getOauthUrl($val['url']);
				$menuArray['button'][$key]['url'] = $url;
			}

			if( !empty($val['sub_button']) )
			{
				foreach($val['sub_button'] as $item => $value)
				{

					if($value['type'] == 'view')
					{
						$url = $this->getOauthUrl($value['url']);
						$menuArray['button'][$key]['sub_button'][$item]['url'] = $url;
					}
				}
			}
		}

		return json_encode($menuArray);
	}

	/**
	 * @brief oauth请求code
	 * @param string $url 跳转的URL参数
	 */
	public function getOauthUrl($url)
	{
		if ( substr($url,0,7) != 'http://' )
			$url = IUrl::getHost().$url;
		$url = str_replace("weixin.php","index.php",$url);

		$state = urlencode($url);
		$callback = IUrl::getHost().'/weixin.php';
		$url = $this->weObj->getOauthRedirect($callback,$state,'snsapi_userinfo');
		return $url;
	}



}
