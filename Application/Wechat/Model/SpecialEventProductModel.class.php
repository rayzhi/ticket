<?php

namespace Wechat\Model;
use \Common\Lib\Model\DrugsalesbaseModel;

class SpecialEventProductModel extends DrugsalesbaseModel{
	/**
	 * 获取产品信息
	 */
	public function getProduct($productId){
		$data = $this->find($productId);
		return $data;
	}
}