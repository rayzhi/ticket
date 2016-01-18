<?php

namespace Wechat\Controller;
use Think\Controller;

class TestController extends Controller {

    public function testMenuAct(){
        print_r(\Wechat\Logic\MenuLogic::initMenu());
    }

}
