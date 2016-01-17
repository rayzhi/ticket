<?php

namespace Admin\Model;
use Think\Model;

class RoleModel extends Model{
    
    protected $tableName = 'role';
	
	const SUPER_ADMINISTRATOR = 1; //超级管理员

	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('name', 'require', '角色名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
	);
	
	//自动完成
	protected $_auto = array(
			array('create_time', 'time', 1, 'function'),
			array('update_time', 'time', 2, 'function'),
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
	public function get_role_list(){
		$data = $this->order(array("listorder" => "asc", "id" => "desc"))->select();
		return $data;
	}
	
	public function simple_role_list($currentid = null){
		$cond['status'] = 1;
		$cond['id'] = array('NEQ', 1);
		$data = D('Role')->where($cond)->field('id, name')->select();
		if($currentid){
			foreach($data AS $k => $v){
				if($v['id'] == $currentid){
					$data[$k]['current'] = 1;
				}else{
					$data[$k]['current'] = 0;
				}
			}
		}
		return $data;
	}
}