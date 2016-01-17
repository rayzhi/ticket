<?php 

namespace Admin\Admin;

class PublicAdmin extends CommonAdmin {
	
	public function indexAct() {
		//如果通过认证跳转到首页
		//redirect(__BASE__.'admin');
	}
    
	public function dsAct(){
		$g_params = '';
	
		$g_params['__G_ROOT__'] = __ROOT__;
		$g_params['__G_APP__'] = __APP__;
		$g_params['__G_GROUP__'] = __GROUP__;
		$g_params['__G_URL__'] = __URL__;
	
		($c=C('TMPL_PARSE_STRING.__BASE__')) && $g_params['__G_BASE__'] =$c;
		($c=C('TMPL_PARSE_STRING.__STATIC__')) && $g_params['__G_STATIC__'] =$c;
		($c=C('TMPL_PARSE_STRING.__UPLOAD__')) && $g_params['__G_UPLOAD__'] =$c;
	
		foreach ($g_params as $n=>$v){
			$content .= "var $n = \"" . addcslashes($v, "\"'") . "\";\n";
		}
	
		echo $content;
	}

   // 用户登录页面
    public function loginAct() {
        if(!$this->auth->isLogin()) {
        	$cookie_rember = cookie('remember');
        	$this->assign('err', I("get.err"));
        	$this->assign('msg', I("get.msg"));
        	$this->assign('usr', ($usr=I("get.usr"))?$usr:($cookie_rember?$cookie_rember:''));
        	$this->assign('redirect', get_redirect(__BASE__).'admin');
        	$this->assign('remember', $cookie_rember || $cookie_rember===null?1:0);
            $this->display();
        }else{
            $this->redirect(get_redirect(__BASE__).'index.php/admin');
        }
    }
    
    // 用户登出
    public function logoutAct() {
        if($this->auth->isLogin()) {
            $this->auth->logout();
        }
        redirect(get_redirect(__BASE__).'index.php/admin', 0, TL('成功登出！'));
    }

    // 登录检测
    public function postLoginAct() {
    	$aClean = array();
    	
    	$aClean['account'] = I('post.account');
    	$aClean['password'] = I('post.password');
    	$aClean['captcha'] = I('post.captcha');
    	$aClean['remember'] = I('post.remember', 'intval');
    	$redirect = get_redirect(__BASE__).'admin';
    	
        if(empty($aClean['account'])) {
        	$this->error('账号必须');
        }elseif (empty($aClean['password'])){
        	$this->error('密码必须');
        }elseif (empty($aClean['captcha'])){
        	$this->error('验证码必须输入');
            $this->auth->redirectLogin(array('err'=>TL('验证码必须输入'), 'usr'=>$aClean['account']), $redirect);
        }elseif(!check_verify($aClean['captcha'])) {
        	$this->error('验证码输入错误');
        }
        
        //生成认证条件
        $userInfo = D('AdminAccount')->verifyLogin($aClean['account'], $aClean['password']);

        //使用用户名、密码和状态的方式进行认证
        if(false === $userInfo) {
        	$this->error('账号不存在');
        }elseif(empty($userInfo)){
        	$this->error('密码错误');
        }else {
            $this->auth->login($userInfo['uid'], $userInfo);
            
            // 记录最后登录信息
        	D('AdminAccount')->lastLogin($userInfo['uid']);
        	
        	// 是否记住用户名
        	if($aClean['remember']){
        		cookie('remember', $userInfo['account'], time()+3600*30);
        	}else{
        		cookie('remember', 0);
        	}
        	$this->success('登录成功！', UC('Index/index'));
        }
    }
    
    // 验证码
    public function captchaAct() {
    	$verify = new \Think\Verify();
    	$verify->entry(1);
    }	
    
    public function successAct(){
    	$this->success('成功添加！');
    }
    
    protected function _requireLogin(){
    	if(!$this->auth->isLogin()) {
    		$this->auth->redirectLogin("操作需要登录才能进行");
    	}
    }
    
    public function passwordAct(){

    	$this->_requireLogin();
    	
 
    	if(!IS_POST){
    		//$account = M('AdminAccount')->field('account')->find();
    		//$account = $account['account'];
    		//$this->assign('account',$account);
    		$this->display();
    	}else{
    		$aClean = array();
    		$aClean['oldpass'] = I('post.oldpass');
    		$aClean['newpass'] = I('post.newpass');
    		$aClean['retypepass'] = I('post.retypepass');
    		
    		if(!$aClean['oldpass'] || !$aClean['newpass'] || !$aClean['retypepass']){
    			$this->error(TL("操作失败：请完整输入所有必填项"), __ACTION__);
    		}
    		
    		$oAccount = D('AdminAccount');
    		
    		// 验证新密码是否一致
    		if(strcmp($aClean['newpass'], $aClean['retypepass'])){
    			$this->error(TL("操作失败：新密码两次输入不一致"), __ACTION__);
    		}
    		
    		// 验证旧密码是否正确
    		if(!$oAccount->verifyLogin($this->_account['account'], $aClean['oldpass'])){
    			$this->error(TL("操作失败：输入的旧密码不正确"), __ACTION__);
    		}
    		
    		// 修改为新密码
    		$oAccount->changePassword($this->_account['uid'], $aClean['newpass']);
    		
    		// 自动退出，要求重新登陆
    		$this->auth->logout();
    		
    		$this->success(TL("成功修改密码！请使用新密码重新登陆。"), UC('Admin/Public/login'));
    		
    	}
    }
    
    
}