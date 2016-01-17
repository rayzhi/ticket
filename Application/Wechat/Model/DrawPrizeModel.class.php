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

class DrawPrizeModel extends CommonModel {
	
	//自动验证
	protected $_validate = array(
			array('awards_name', 'require', '奖项名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('prize_name', 'require', '奖品名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('prize_num', 'require', '奖品数量不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('probability', 'require', ' 中奖概率 不能为空', 1, 'regex', CommonModel:: MODEL_BOTH ),
			array('prompt', 'require', ' 中奖信息不能为空', 1, 'regex', CommonModel:: MODEL_BOTH ),
	);
	
	//自动完成
	protected $_auto = array(
			array('add_time', 'time', 1, 'function'),
	);
	
	
	
	
}