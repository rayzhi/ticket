<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class TextReplyLogic extends Model{

	public function get(){
		return 'test';
	}


/*
取出某一条新闻

hao  2014-4-21 12:44
*/
	public function getNews($obj_id=0){
		$content = array();

		if ( empty($obj_id))
			return $content;
		
		$info = D('Home/Wordpress')->detail($obj_id);
		if (empty($info))
			return $content;

// 修改为在微信上显示 wap 页面
		$url = 'http://'.$_SERVER['SERVER_NAME'].U('Wap/News/info',array('id'=>$obj_id));
		$url = str_replace('weixin.php','index.php',$url);

		$post_title 		= strip_tags($info['post_title']);
		$post_content 	= strip_tags($info['post_content']);
		$post_content	= htmlspecialchars_decode($post_content);
		$post_content 	= str_replace('&nbsp;','',$post_content);
		$post_content 	= mb_substr($post_content, 0, 80, 'utf-8');


//		$post_content .= "\n".$url;		
//			'PicUrl'=>'http://gdrs.flie.cn/wp-content/uploads/2014/03/d7e42a99ce9aca8790a29b64d677eb0c-300x224.jpg',
		$picurl = 'http://meigainian.com/apps/thumb.php?w=640&h=320&url=2014/06/10/17/5396ca3185411.jpg';

		$content[] = array(
			'Title'=>		$post_title,
			'Description'=>	$post_content,
			'PicUrl'=> $picurl,
			'Url'=> $url
		);		
		
		return $content;
	}


/*
取出多条新闻

hao 2014-4-21  12:45
*/	
	public function getMoreNews($options = NULL){
		$content = array();
		if ( empty($options))
			return $content;
		
		$info = D('Home/Wordpress')->lists($options);
		if ( empty($info))
			return $content;
		
		$i_count;
		foreach($info as $k => $v ){
// 不能超过预设的最大数值				
			if ( $i_count > $this->wechat_options['wechat_maxnum'] )
				break;
				
			$post_id	 			= strip_tags($v['ID']);
			$post_title 			= strip_tags($v['post_title']);
			$post_content 	=  strip_tags($v['post_content']);
			$post_content	= htmlspecialchars_decode($post_content);
			$post_content 	= str_replace('&nbsp;','',$post_content);
			$post_content 	= mb_substr($post_content, 0, 120, 'utf-8');
			

			$url = 'http://'.$_SERVER['SERVER_NAME'].U('Wap/News/info',array('id'=>$obj_id));
			$url = str_replace('weixin.php','index.php',$url);
// 				'PicUrl'=>'http://meigainian.com/apps/thumb.php?w=640&h=320&url=2014/06/10/17/5396ca3185411.jpg',

			$picurl = 'http://meigainian.com/apps/thumb.php?w=640&h=320&url=2014/06/10/17/5396ca3185411.jpg';

			$content[] = array(
				'Title'=>		$post_title,
				'Description'=>	$post_content,
				'PicUrl'=> $picurl,
				'Url'=> $url
			);
			$i_count++; // 计数				
		}
	
		return $content;
	}


}
