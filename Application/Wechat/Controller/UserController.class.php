<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class UserController extends CommonController {

    public function indexAct(){
        
        $userInfo = D('User')->getUserInfo();
        $this->assign('userInfo',$userInfo);
        
        $this->display();
        
    }
    
    public function orderDetailAct(){
        
        $this->display();
    }
    
    public function couponAct(){
        
        $coupon = D('UserCoupon')->getCoupon();
        $this->assign('coupon',$coupon);

        $this->display();
    }
    
   

}
