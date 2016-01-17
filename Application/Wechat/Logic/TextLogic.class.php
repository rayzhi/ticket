<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class TextLogic extends Model{

	Protected $autoCheckFields = false;

	public function get(){
		return 'test';
	}

	public function setTextReply($key=NULL)
	{
	    
	    $content .= '欢迎关注乐练部落哦~，正在开发中呢~';
	    $_return['type'] = 'text';
	    $_return['content'] = $content;
	    return $_return;
	    
		$_return = array();
		if (empty($key))
			return $_return;
		

		if ( is_numeric($key))
		{
			$content .= '您输入的是数字．'.$key;
			$_return['type'] = 'text';
			$_return['content'] = $content;
			return $_return;			
		}
		
		

		$picurl = 'http://w.szzxue.com/index.php?controller=pic&action=thumb&h=200&img=upload@_@2015@_@05@_@01@_@20150501040806745.jpg&w=200';
		$post_title = '乐练部落';
		$post_content = '乐练部落';
		$url = 'http://w.szzxue.com/index.php?controller=site&action=products&id=3';

		$content[] = array(
			'Title'=>		$post_title,
			'Description'=>	$post_content,
			'PicUrl'=> $picurl,
			'Url'=> $url
		);		
		$_return['type'] = 'news';
		$_return['content'] = $content;
		return $_return;			
		


		
		$content = '您输入的是:';
		$content .= $key;
		$content .= "\n";
//		$content .= $this->revFrom;
//		$content .= "\n";
//		$content .= $this->revType;
//		$content .= $this->revEvent['event'];
//		$content .= "\n";
//		$content .= serialize($this->userInfo);
//		$content .= "\n";
		$content .= '系统找不到匹配的关键词，请重新输入．';
		$_return['type'] = 'text';
		$_return['content'] = $content;
		return $_return;			

	}

/*
查询关键词
自动回复表在 ts_wechat_auto_reply 中

hao 2014-5-12 09:53
*/
	public function searchKey($key=''){
		$_return = array();


		if (empty($key))
			return $_return;

		if ( is_array($key))
			return $_return;
		

		$map['status'] = 1;
		$map['userkey'] = strtolower($key);

		$info = M('WechatAutoReply')->where($map)->find();
		if(false === $info){
			return $_return;
		}

		$reply_content = unserialize($info['content']);
		$_return['type'] = $info['type'];
		
		if ( $info['type'] == 'text' ){
			$_return['content'] = $reply_content['text'];
		}
		
		if ( $info['type'] == 'news' ){
			$content[] = array(
				'Title' =>					$reply_content['title'],
				'Description' =>		$reply_content['description'],
				'PicUrl' => 				$reply_content['picurl'],
				'Url' => 						$reply_content['url']
			);	

			$_return['content'] = $content;
		}
		
		return $_return;			
	}


}
