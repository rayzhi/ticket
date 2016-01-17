<?php
/**
 * 药师帮后台----后台公用控制器
 *
 * PHP version 5.5
 *
 * @category	qrug
 * @package     Pharmacist
 * @subpackage  config
 * @copyright   2014 GZSD
 * @version     SVN: $Id: class.php 43 2014-12-12 16:23:59 Cengp $
 */
namespace Chat\Chat;
use \Common\Lib\Pclass\Webown;

class ShakeChat extends CommonChat {
	
	protected $_cmdString;
	protected $_cmdOutput;
	protected $_cmdStatus;
	
    public function indexAct(){
    	$iswin = strtolower(substr(PHP_OS, 0, 3))=='win'?true:false;
    		
    	if($iswin){
    		$this->_cmdString = "start /b E:/wamp/bin/php/php5.5.12/php H:/net_project/meeting/public/meeting.php Public/test 2>&1";
    		$pipe = popen($this->_cmdString , 'r');
    		if (!$pipe) {
    			$this->_cmdStatus = 0;
    		}else{
    			pclose($pipe);
    		}
    		$this->_cmdStatus;
    	}else{
    		$this->_cmdString = "/opt/app/php/bin/php /opt/web/communal/public/communal.php /Admin/Public/test/mobile/13828460969 > /tmp/cronOigboi 2>&1 &";
    		$this->_cmdOutput = system($this->_cmdString, $this->_cmdStatus);
    		echo $this->_cmdStatus;
    	}
    	exit;
    	$Webown = new Webown();
    	$Webown->run_cron('Public', 'test');
    	exit;
    	$this->_push_text();
    	$openid = $this->_param('openId');
    	$id = $this->_param('id');
    	if(empty($openid)){
    		$this->error('请从微信浏览！');
    	}
    	$status = M('Draw')->where('id = '.$id)->getField('status');
    	if($status == 2){
    		$this->error('本次抽奖已结束，！');
    	}else if($status == 0){
    		$this->error('抽奖尚未开始！');
    	}else{
	    	$count = M('DrawRecord')->where("openid = '$openid' AND draw_id = $id")->count();
	    	if(!empty($count)){
	    		$this->error('您已经抽过奖了！');
	    	}
	    	$this->assign('id',      $id);
	    	$this->assign('openid',  $openid);
	    	$this->display();
    	}
    }
    
    public function shakeAct(){
    	$this->_push_text();
    	$id = I('get.id');
    	//获取头像
    	//$data = M('Userinfo')->field('avatar')->order('id desc')->limit(1000)->select();
// 		$sql = "SELECT a.avatar FROM mt_userinfo a, mt_hxsign b WHERE a.openid = b.openid ORDER BY a.id DESC";
// 		$data = M()->query($sql);
//     	$num = count($data); 
// 	    $this->assign('data', $data);
// 	    $this->assign('id',      $id);
// 	    $this->assign('num',      $num);
    	$this->display();
    }
    
    public function shackAct(){
    	$data = array();
    	$data['openid'] = $this->_param('openid');
    	$data['4susername'] = 'huaxin';
    	$data['draw_id'] = $this->_param('id');
    	$data['draw_time'] = time();
    	$count1 = M('DrawRecord')->where("openid = '{$data['openid']}' AND draw_id = ".$data['draw_id'])->count();
    	if(!empty($count1)){
    		$this->ajaxReturn(0, '已经抽过奖的用户',1);
    	}
    	M('DrawRecord')->add($data);
    	//中过奖的用户不能在中奖
    	$count2 = M('WinningLog')->where("openid = '{$data['openid']}'")->count();
    	if($count2 != 0){
    		$this->ajaxReturn(0, '不能再中奖！',1);
    	}
    	
    	//$count = M('Hxsign')->where("openid = '{$data['openid']}'")->count();
		$sql = "SELECT count(*) AS num FROM mt_hxsign a, mt_userinfo b WHERE a.openid = '{$data['openid']}' AND a.openid = b.openid";
		$arr = M()->query($sql);
    	if(!empty($arr[0]['num'])){
    		$winnun = M('WinningLog')->where("draw_id = ".$data['draw_id'])->count();
    		$ifwin = M('WinningLog')->where("openid = '{$data['openid']}'")->count();
    		$number = M('Draw')->where("id = ".$data['draw_id'])->getField('frequency');
    		if($winnun<$number && $ifwin == 0){
    			M('WinningLog')->add($data);
    			$fakeid = M('Userinfo')->where("openid = '{$data['openid']}'")->getField('fakeid');
    			system("php /data/www/meeting/Command/push_win.php ".$fakeid." > /data/www/meeting/Data/Runtime/Logs/msg.log &");
    		} 
    	}
    	$this->ajaxReturn(0, '抽奖完成',1);
    }
    
    public function ifwinAct(){
    	$openid = $this->_param('openid');
    	$id = $this->_param('id');
    	$count = M('WinningLog')->where("openid = '$openid' AND draw_id = $id")->count();
    	if(!empty($count)){
    		$this->ajaxReturn(0, '恭喜你中奖了',1);
    		$data['status'] = 1;
    	}else{
    		$this->ajaxReturn(0, '遗憾，你没有中奖',1);
    	}
    }
    
    public function paywinAct(){
    	$id = $this->_param('id');
    	//如果中奖的人数不够，从hxsign里面抽出数据补充
    	$number = M('Draw')->where("id = ".$id)->getField('frequency');
    	$winnun = M('WinningLog')->where("draw_id = ".$id)->count();
    	if($winnun < $number){
    		$cha = $number - $winnun;
    		for($i=1;$i<=$cha;$i++){
    			unset($res);
    			$sql = "SELECT openid FROM mt_hxsign 
    					WHERE openid NOT IN (SELECT openid FROM mt_winning_log) 
    					LIMIT 1";
    			$res = M()->query($sql);
    			$data = array();
    			$data['openid'] = $res[0]['openid'];
    			$data['4susername'] = 'huaxin';
    			$data['draw_id'] = $id;
    			$data['draw_time'] = time();
    			M('WinningLog')->add($data);
    			$fakeid = M('Userinfo')->where("openid = '{$data['openid']}'")->getField('fakeid');
    			system("php /data/www/meeting/Command/push_win.php ".$fakeid." > /data/www/meeting/Data/Runtime/Logs/msg.log &");
    		}
    	}
    	$sql = "SELECT DISTINCT(a.log_id), c.nickname AS name, right(b.mobile,4) AS mobile , left(b.mobile,3) AS premob, c.avatar, c.nickname FROM 
    			mt_hxsign b LEFT JOIN mt_userinfo c ON b.openid = c.openid,
    			mt_winning_log a WHERE a.draw_id = $id
    			AND a.openid = b.openid";
    	$arr = M()->query($sql);
    	$this->assign('arr',   $arr);
    	$this->assign('id',    $id);
    	$this->display();
    }
    
    public function funnyAct(){
//     	$arr = M('WinningLog')->field('openid')->where('draw_id = 4')->select();
//     	print_r($arr);
//     	foreach($arr AS $v){
//     		$fakeid = M('Userinfo')->where("openid = '{$v['openid']}'")->getField('fakeid');
    		$fakeid = 2945411621;
    		system("php /data/www/meeting/Command/pushfunny.php ".$fakeid." >> /data/www/meeting/Data/Runtime/Logs/msg.log &");
//     	}
        echo $fakeid;
    	echo '12345';
    }
    
    
}
