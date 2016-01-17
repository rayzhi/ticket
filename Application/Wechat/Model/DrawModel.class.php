<?php
/**
 * 药师帮后台公共用户操作记录模型
 * 
 * PHP version 5
 * 
 * @category	ysbang
 * @package     ysbang
 * @subpackage  Model
 * @version     SVN: $Id: AdminAccountModel.class.php 10 2014-12-16 10:34:05Z cengp $
 */
namespace Admin\Model;

class DrawModel extends CommonModel {
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('name', 'require', '抽奖活动名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('win_num', 'require', '中奖人数不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('start_time', 'require', '抽奖活动生效时间不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('end_time', '_check_endtime', '抽奖活动失效时间不能为空且必须大于生效时间！', 1, 'callback', CommonModel:: MODEL_BOTH ),
	);
	
	//自动完成
	protected $_auto = array(
			array('add_time', 'time', 1, 'function'),
			array('start_time', 'strtotime', 3, 'function'),
			array('end_time', 'strtotime', 3, 'function'),
	);
	
	//检查抽奖活动生效时间失效时间
	protected function _check_endtime(){
		$begin = strtotime($_POST['start_time']);
		$end = strtotime($_POST['end_time']);
		if($end && $begin <= $end){
			return true;
		}
		return false;
	}
	
	
	
}