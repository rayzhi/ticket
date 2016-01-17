<?php

namespace Wechat\Controller;
use Think\Controller;

class IndexController extends Controller {

	/**
	 * 文章详情页
	 */
    public function indexAct(){


        echo '欢迎到来。';
        exit();

    	$id = I('get.id');
    	if(!$id){
    		$this->error('非法操作！');
    	}
    	$info = M('Article')->where(array('id' => $id))->find();
    	$this->assign('info',  $info);
    	$this->display();
    }
    
    
}
