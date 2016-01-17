<?php

namespace Wechat\Model;
use \Common\Lib\Model\OrderbaseModel;

class SpecialEventOrderItemsModel extends OrderbaseModel{
	/**
	 * 根据订单号获取产品列表
	 */
	public function getItems($orderId){
		$data = $this->where(array('order_id'=>$orderId))->find();
		return $data;
	}
}