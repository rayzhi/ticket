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
namespace Chat\Chat;

class SignChat extends CommonChat {
	
    public function signinAct(){
    	$openid = I('get.openid');
    	if(empty($openid)){
    		$this->error('请从微信浏览！');
    	}
    	$count = M('Signin')->where("openid = '$openid'")->count();
    	if(empty($count)){
	    	$list = M('Region')->field('region_id, region_name')->where('parent_id = 1')->select();
	    	$this->assign('list', $list);
	    	$is_guest = I('get.is_guest')?I('get.is_guest'):0;
	    	$this->assign('is_guest', $is_guest);
	    	$this->assign('openid', $openid);
	    	$this->display();
    	}else{
    		$this->aleadyAct();
    	}
    }
    
    public function signAct(){
    	if(IS_POST){
    		$data['username'] = $this->_username;
	    	$data['name'] = I('post.name');
	    	$data['is_guest'] = I('post.is_guest');
	    	$data['openid'] = I('post.openid');
	    	$data['mobile'] = I('post.phone');
	    	$data['region_id'] = I('post.region_id');
	    	$data['is_guest'] = I('post.is_guest');
	    	$data['sign_time'] = time();
	    	if(M('Signin')->add($data)){
	    		$this->success('签到成功！', UC('Sign/aleady'));
	    	}else{
	    		$this->error('签到失败，请重新提交');
	    	}
    	}else{
    		$this->error('非法操作！');
    	}
    }
    
    public function aleadyAct(){
    	$this->display("Sign:aleady");
    }
    
    /**
     * 签到欢迎动画
     */
    public function welcomeAct(){
    	$this->display();
    }
    
    /**
     * 回调获取新签到用户
     */
	public function lastestAct(){
    	$arr = M('Userinfo')->field('avatar,id')->where('remarkname = 1')->find();
    	$data = array();
    	$arrq = array();
    	$arrq['remarkname'] = 2;
    	if(!empty($arr)){
    		M('Userinfo')->where("id = ".$arr['id'])->data($arrq)->save();
    		$data['picUrls'][] = 'http://test.wemeeting.cn/meeting/Public/uploads/hxhead_img/'.$arr['avatar'];
    		$data['title'] = 1;
    		$data['usercount'] = count($arr);
    	}else{
    		$data['picUrls'][] = '';
    		$data['title'] = 1;
    		$data['usercount'] = 0;
    	}
    	$this->ajaxReturn($data,'JSON');
    }
}
