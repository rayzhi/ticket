<?php

namespace Admin\Model;
use Think\Model;

class AccessModel extends Model {

    protected $tableName = 'access';

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('role_id', 'require', '角色不能为空！', 1, 'regex', 3),
        array('g', 'require', '项目不能为空！', 1, 'regex', 3),
        array('m', 'require', '模块不能为空！', 1, 'regex', 3),
        array('a', 'require', '方法不能为空！', 1, 'regex', 3),
    );

    /**
     * 角色授权
     * @param type $roleid
     * @param type $addauthorize 是一个数组 array(0=>array(...))
     * @return boolean
     */
    public function rbac_authorize($roleid, $addauthorize) {
        if(!$roleid || !$addauthorize || !is_array($addauthorize)){
            return false;
        }
        //删除旧的权限
        $this->where(array("role_id" => $roleid))->delete();
        return $this->addAll($addauthorize);
    }
    
    protected function _before_write(&$data) {
    	parent::_before_write($data);
    }

    /**
     * 获取用户的操作权限方法(注：为防止不同控制器的操作名重名，操作名是由模型和方法构成)
     * @param int $roleId
     * @return array ['action'=>1]
     * @author Qiugh 2015-12-22 15:38:58
     */
    public function getRoleOperationAuth($roleId) {
        $tbMenu = \Admin\Model\MenuModel::ADMIN_MENU;
        $superAdministrator = \Admin\Model\RoleModel::SUPER_ADMINISTRATOR; //超级管理员
        //取出所有的操作方法     
        if ($roleId != $superAdministrator) {
            $map["a.role_id"] = $roleId; //非超级管理员时
            $map["b.type"] = \Admin\Model\MenuModel::TYPE_OPERATION;
            $field = array("a.m as model", "a.a as action");               
            $data = $this->alias("a")->join("LEFT JOIN " . $tbMenu . " b ON b.app = a.g AND b.model = a.m AND b.action = a.a")
                              ->where($map)->field($field)->select();
        } else {
            $data = D("Menu")->getOperation();
        }

        $operation = array();
        foreach ($data as $k => $v) {
            $tmp = $v["model"] . "-" . $v["action"]; //由控制器和方法组成的别名
            $operation[$tmp] = 1;
        }

        return $operation;
    }

}

?>