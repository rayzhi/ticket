<?php
/*



*/
namespace Wechat\Logic;
use Think\Model;
class ImageLogic extends Model{

	public function get(){
		return 'test';
	}

/*
�ظ�
*/	
	public function setImageReply()
	{
		$content = 'Ŀǰ�˹��ܻظ���û�п���';
		$_return['type'] = 'text';
		$_return['content'] = $content;
		return $_return;
	}


}
