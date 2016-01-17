<?php
/**
 * 药师帮后台----后台公用控制器
 *
 * PHP version 5.5
 *
 * @category	meeting
 * @package     Chat
 * @subpackage  siginin
 * @copyright   2014 GZSD
 * @version     SVN: $Id: class.php 43 2014-12-12 16:23:59 Cengp $
 */
namespace Wechat\Controller;
use Think\Controller;

class UserController extends Controller {

	
	
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
