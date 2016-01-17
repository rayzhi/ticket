<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class VideoLogic extends Model{

	public function get(){
		return 'test';
	}


/*
回复
*/	
	public function setVideoReply()
	{
		$content = '目前此功能回复还没有开放';
		$_return['type'] = 'text';
		$_return['content'] = $content;
		return $_return;
	}



}
