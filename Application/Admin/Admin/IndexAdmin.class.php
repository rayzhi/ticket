<?php

namespace Admin\Admin;

class IndexAdmin extends CommonAdmin {

    public function indexAct(){
    	if($this->_account['role_id']){    	    
    		$menu_data = D("Menu")->menu_json($this->_account['role_id']);
    		$res = '<ul class="nav nav-list">';
    		$res .= $this->tpl_code_right_sidebar($menu_data);
    		$res .= '</ul>';
    		$this->assign("SUBMENU_CONFIG", $res);
    	}
        layout(false);
        $this->display();
    }
    
    public function welcomeAct(){
    	$this->meta_title = '管理首页';
    	
    	$this->display();
    }
    

}
