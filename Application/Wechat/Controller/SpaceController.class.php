<?php

/**
 * 场地模块
 * 
 */
namespace Wechat\Controller;

class SpaceController extends CommonController {

    public function indexAct(){
        $list = D('space_area')->select();
        $this->assign('list',$list);
        $this->display();
    }


}
