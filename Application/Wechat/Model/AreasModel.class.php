<?php
/**
 * 药师帮地区模型
 * 
 * PHP version 5
 * 
 * @category    ysbang
 * @package     ysbang
 * @subpackage  Model
 * @version     SVN: $Id: MenuModel.class.php 10 2014-102-16 10:34:05Z cengp $
 */
namespace Wechat\Model;
use \Common\Lib\Model\DictionarybaseModel;

class AreasModel extends DictionarybaseModel{

	/**
	 * 根据地区ID查找地区名
	 * @param $areaId 地区ID号
	 */
	public function getInfosById($areaId){
		$whereMap['id'] = $areaId;
		$data = $this->where($whereMap)->find();

		return $data;
	}

	/**
	 * 获取下级地区列表
	 * @param $areaCode 地区国家编码
	 */
	public function getNextArea($areaCode){
		$whereMap['parent_id'] = $areaCode;
		$fieldMap = array('id','area_name');
		$data = $this->where($whereMap)->field($fieldMap)->select();

		return $data;
	}
}

?>