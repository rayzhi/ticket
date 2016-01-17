<?php
namespace Web\Controller;
use Think\Controller;
class CommonController extends Controller {
	
    //初始化
    public function _initialize() {
        
    }
    
    protected function _getWeekDay(){
    	$weekarray = array("日","一","二","三","四","五","六");
    	return "星期".$weekarray[date("w")];
    }
    
}