<?php
namespace Web\Controller;

class IndexController extends CommonController {
    
	/**
	 * 官网首页
	 */
    public function index(){
		
    	$info['now']     = date('Y年m月d日').$this->_getWeekDay();//日期
        $info['notice']  = D('WstContent')->getContent(0,4,9); //公告通知
        $info['latest']  = D('WstContent')->getContent(0,5,7); //最新资讯
        $info['pubInfo'] = D('WstContent')->getContent(0,4,330); //信息公开
        $info['leader']  = D('WstLd')->getContent(0,1); //领导

        $this->assign('info',$info);
    	
    	$this->display();
    	
    }
	
}