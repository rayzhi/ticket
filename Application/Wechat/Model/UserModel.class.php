<?php

namespace Wechat\Model;
use Think\Model;

class UserModel extends Model{
    
    protected $tableName = 'user';
    CONST USER = 'user';
 
    public function getUserInfo(){
        return $this->where(array('openid'=>session('openid')))->find();
    }


}