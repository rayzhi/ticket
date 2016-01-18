<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;

class TicketController extends CommonController {

    /**
     * 魔幻城首页
     */
    public function indexAct(){
        
        $this->display();
        
    }
    
    /**
     * 列表
     */
    public function tickeListAct(){
        
        
        
        $this->display();
        
    }
    
    /**
     * 下单
     */
    public function makeOrderAct(){
        
        if(IS_POST){
            
            
        }else{
            $this->error('非法操作！');
        }
        
    }
    
   

}
