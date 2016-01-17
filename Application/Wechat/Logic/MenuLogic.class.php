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
	 * @brief ��ȡ�Զ���˵�
	 * @return array
	 */
/*
�������³���
ע���Ǵ�json�����ļ�ȡ����
����ת��Ϊ��Ĭ����ַ
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
	 * @brief ���²˵�
	 * @param json $menuData �˵����� {"button":[{"name":"����","sub_button":[{"type":"view","name":"�Ӳ˵�����","url":"http://www.aircheng.com"}]}]}
	 * @return array("errcode" => 0,"errmsg" => "ok")
	 */
	public function setMenu($menuData)
	{
		//URL��Ĭ��¼�滻����
		$menuData = $this->converOauthUrl($menuData);



		$classFileBase = IWeb::$app->getBasePath().'plugins/wechat/wechat_base.php';
// wechat ���࣬ǿ��ĵ��������
		if(is_file($classFileBase))
		{
			include_once($classFileBase);

		    $options = array(
					'token'=>	$this->wechat_options['token'], //��д���趨��key
					'appid'=>	$this->wechat_options['appid'], //��д�߼����ù��ܵ�app id
		 			'appsecret'=>$this->wechat_options['appsecret'], //��д�߼����ù��ܵ���Կ
		 	);

			$this->weObj = new wechat_base($options);
		}

		$this->weObj->deleteMenu();
		$_return = $this->weObj->createMenu($menuData);
		return $_return ;
	}

	//URL��Ĭ��¼�滻����
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
	 * @brief oauth����code
	 * @param string $url ��ת��URL����
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
