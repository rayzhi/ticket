<?php

namespace Admin\Admin;
use \Admin\Auth as t;
use Think\Controller;
use Common\Lib\Pclass as p;

class CommonAdmin extends Controller {
	
	protected $auth = null;
	/**
	 * 当前登录帐户信息
	 * @var array
	 */
	protected $_account = array();
	protected $_page = 1;
	protected $_pagesize = 10;
	
	public function _initialize() {
		// 模板全局变量
		$aConfVars = array(
				'TITLE', 'SITE_TITLE', 'SITE_COMPANY',
				'VAR_REDIRECT',
		);
		$aTplVars = array();
		foreach($aConfVars as $v){
			$aTplVars[strtolower($v)] = tl(C($v));
		}
		$this->assign('G_VAR', $aTplVars);

		// 登录认证判断
		$oAminAuth = $this->auth = new t\AdminAuth();
		if($oAminAuth->isLogin()){
			$this->_account = $oAminAuth->getUser();
			$this->assign('ACCOUNT', $this->_account);
			
			//记录用户每次操作post请求数据
			$uid = $this->_account['uid'];
			if (IS_POST) {
				$this->_postLog();
			}
			
			if(!strcasecmp(CONTROLLER_NAME, 'Index')){
				
			}elseif(!strcasecmp(CONTROLLER_NAME, 'Public')){
				
			}else{
				if(!$this->check_access($this->_account['role_id'])){
					$this->error("您没有访问权限！", null, -1); //不需要跳转
					exit();
				}				
			}
		}else{
			if(strcasecmp(CONTROLLER_NAME, 'Public')){
			    
				$tipMsg = (strcasecmp(ACTION_NAME, 'login'))?"操作需要登录才能进行":'';
				$oAminAuth->redirectLogin($tipMsg);
					// 注意：代码永远运行到此处，调用redirect后，程序已经exit	
			}
		}
	}
	
	/**
	 * 将post信息记录到文件中
	 */
	private function _postLog(){
		$uid = $this->_account['uid']; //用户ID
		//记录信息
		$data = $_POST;
		unset($data['Data']);
		$info  = "[" . date('H:i:s') . "] ";
		$info  .= 'uid为'.$uid.'的用户在操作'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'中请求的数据为'.print_r($data, true);
		check_dir(RUNTIME_PATH . 'Logs/operation');
		file_put_contents(RUNTIME_PATH . 'Logs/operation/'.date('Ymd') . '.log',  $info . "\r\n", FILE_APPEND);
	}
	
	/**
	 * 传入菜单结构数组
	 * 生成菜单部分html赋值到页面
	 */
	protected function tpl_code_right_sidebar($menu_data){
		foreach($menu_data as $menu){
			unset($class);
			unset($class1);
			unset($icon);
			unset($icon1);
			if($menu['model'] == CONTROLLER_NAME && $menu['action'] == ACTION_NAME){
				$class = 'class="active"';
				$icon1 = $menu['icon']?$menu['icon']:'double-angle-right';
			}
			if(empty($menu['items'])){
				$res .= '<li '.$class.'>';
				$res .= '<a href="javascript:openapp(\''.$menu['url'].'\',\''.$menu['id'].'\',\''.$menu['name'].'\');">
    					 <i class="icon-'.$icon1.'"></i>
    					 <span class="menu-text">'.$menu['name'].'</span>
    					 </a>';
				$res .= '</li>';
			}else{
				if($menu['parent'] == '' && $menu['model'] == CONTROLLER_NAME){
					$icon = $menu['icon']?$menu['icon']:'double-angle-right';
					$class1 = 'class="active open"';
				}
				if($menu['parent'] != '' && $menu['model'] == CONTROLLER_NAME){
					$icon = $menu['icon']?$menu['icon']:'leaf';
					$class1 = 'class="open"';
				}else{
					$icon = $menu['icon']?$menu['icon']:'double-angle-right';
				}
				$res .= '<li '.$class1.'>';
				$res .= '<a href="#" class="dropdown-toggle">
    					<i class="icon-'.$icon.'"></i>
    					<span class="menu-text">'.$menu['name'].'</span>
    					<b class="arrow icon-angle-down"></b>
    					</a>
    					<ul  class="submenu">
    						'.$this->tpl_code_right_sidebar($menu['items']).'
    					</ul>';
				$res .= '</li>';
			}
	
		}
		 
		return $res;
	}
	
	/**
	 * 后台用户操作记录
	 * @param number $datakey 涉及的数据记录id
	 * @param string $subject  日志标题
	 * @param string $summary  日志摘要
	 * return boolean
	 */
	protected function _admin_log($datakey=0, $subject=true, $summary=false, $params=null){
		$aSet = array();
		$aSet['uid']      = $this->_account['uid'];
		$aSet['account']  = $this->_account['account'];
		$aSet['action']   = ACTION_NAME;
		$aSet['datakey']  = $datakey;
		$aSet['logtime']  = date('Y-m-d H:i:s');
		$aSet['logip']    = get_client_ip();
		$aSet['category'] = CONTROLLER_NAME;
		 
		if($subject) $aSet['subject'] = $subject;
		if($summary) $aSet['summary'] = $summary;
		if($params)  $aSet['params']  = $params;
		D('AdminLog')->add($aSet);
	}

	/**
     * 记录后台操作日志
     * @param string $message 日志信息
     * @param string $file 日志文件
     * @return number
     */
    protected function _log($info, $file = NULL){
    	check_dir(RUNTIME_PATH . 'Logs/admin');
    	file_put_contents($file?$file:(RUNTIME_PATH . 'Logs/admin/'.'admin_' . date('Ymd') . '.log'), "[" . date('H:i:s') . "] " . ( (is_array($info) || is_object($info)) ?print_r($info, true):$info ) . "\n" , FILE_APPEND);
    }

	/**
	 * 获取用户凭证(包括获取超级用户的用户凭证)
	 * 超级用户ID：-1(程序内部推送消息需要用到)
	 * @param $userId 用户ID   -1：获取超级用户的用户凭证
	 * @param $expire 期限(秒) -1：没有生存期限，一直保存在Redis中
	 * @return false：生成失败   其它值：$userToken 用户凭证
	 */
	protected function getUserToken($userId,$expire = 3600){
		$maxNums = 7;   // 最多循环生成次数
		$userToken = false;
		$isSuccess = false;  // 记录是否生成userToken成功
		for ($i = 0; $i < $maxNums; $i++) {
			$userToken = generateUserToken($userId);
			$saveUserId = p\MyRedis::get("token.to.userid:$userToken");
			if ($saveUserId) {  // 用户凭证已经存在
				$isSuccess = false;
				continue;
			}else{
				$isSuccess = true;
				if ($expire == -1) {  // 没有生存期限，一直保存在Redis中
					p\MyRedis::set("token.to.userid:$userToken",$userId);
				}else{
					p\MyRedis::set("token.to.userid:$userToken",$userId,$expire);
				}
				break;
			}
		}

		if (!$isSuccess) $userToken = false;  // 生成失败
		return $userToken;
	}

	/**
	 * 将用户凭证、登录方式等用户信息保存到Redis中
	 * usertoken：主要是为了删除旧token，即用户重新登录将获取新用户凭证，旧用户凭证将被删除
	 * logintype：登录类型(1-手机号码  2-邮箱  3-QQ号码  4-Q药网旧用户)
	 * logintime：登录时间(时间戳)
	 * @param $userId       用户ID
	 * @param $userInfo     需要保存的用户信息
	 * @param $time longint 过期时间(S)  默认值为0-不设置过期时间
	 * @return true:成功 flase:失败
	 */
	protected function saveUserInfoToRedis($userId, $userInfo, $time = 0){
		$oldUserToken = p\MyRedis::hashGet("user.info:$userId",'usertoken');
		if ($oldUserToken) {  // 旧用户凭证存在，则删除
			if (p\MyRedis::exists("token.to.userid:$oldUserToken")) {  // 手动删除还没过期的旧用户凭证
				p\MyRedis::delete("token.to.userid:$oldUserToken");
			}
		}

		$data = array();
		if (isset($userInfo['usertoken'])) $data['usertoken'] = $userInfo['usertoken'];
		if (isset($userInfo['logintype'])) $data['logintype'] = $userInfo['logintype'];
		if (isset($userInfo['logintime'])) $data['logintime'] = $userInfo['logintime'];

		$result = false;
		if (!empty($data)) $result = p\MyRedis::hashSet("user.info:$userId", $data, $time);
		return $result;
	}

	/**
	 * 获取超级用户的用户凭证，并将用户凭证保存到Redis中
	 * 超级用户ID：-1(程序内部推送消息需要用到)
	 * 店员版：userid = -1  usertoken = GZSDYSBANGPHARMA      user.info:$userId         token.to.userid:$userToken
	 * 大众版：userid = -1  usertoken = GZSDYSBANGPUBLIC      user.info.pub:$userId     token.to.userid.pub:$userToken
	 * 工业版：userid = -1  usertoken = GZSDYSBANGINDUST      user.info.ind:$userId     token.to.userid.ind:$userToken
	 * @return false：生成失败   其它值：$userToken 用户凭证
	 */
	protected function getSuperUserToken(){
		$userId = -1;  // 超级用户ID：-1(店员版程序内部推送消息需要用到)
		$userToken = p\MyRedis::hashGet("user.info:$userId",'usertoken');
		if (!$userToken) {  // 不存在才生成
			//$userToken = $this->getUserToken($userId,-1);
			// 直接定义16位token(app用户登陆生成token是32位)
			$userToken = 'GZSDYSBANGPHARMA';  // 店员版后台推送专用TOKEN
			p\MyRedis::set("token.to.userid:$userToken",$userId);  // 保存到Redis中

			$userInfo = array();
			$userInfo['usertoken'] = $userToken;
			$userInfo['logintype'] = 0;  // 0：表示程序内部登录，非用户登录
			$userInfo['logintime'] = time();  // 登录时间
			$this->saveUserInfoToRedis($userId, $userInfo);
		}
		return $userToken;
	}

	protected function getCommonUserTokenByUid($userId){
		$userToken = p\MyRedis::hashGet("user.info:$userId",'usertoken');
		if (!$userToken) {  // 不存在才生成
			$expire = 5 * 24 * 3600;   // userToken有效期暂时设置为5天
			$userToken = $this->getUserToken($userId,$expire);

			$userInfo = array();  // 需要保存的用户信息
			$userInfo['usertoken'] = $userToken;
			$userInfo['logintype'] = 0;  // 0：表示程序内部登录，非用户登录
			$userInfo['logintime'] = time();  // 登录时间
			$this->saveUserInfoToRedis($userId, $userInfo, $expire + 60);   // 保存新用户凭证到Redis中(备注：过期时间延长1分钟)
		}
		return $userToken;
	}
	
	/**
	 * 需要用到分页调用此方法对分页参数赋值
	 */
	protected function _set_page(){
		$this->_page = I('post.page')?I('post.page'):$this->_page;
		$this->_pagesize = I('post.pagesize')?I('post.pagesize'):$this->_pagesize;
		$this->_page = ($this->_page-1)*$this->_pagesize;
	}
	
	protected function _get_year_list(){
		
		$startYear = 2013;
		$endYear   = date('Y');
		for($i=$startYear;$i<=$endYear;$i++){
			$year[] = $i;
		}
		return $year;
		
	}
	
	/**
	 * 分页方法
	 * @access protected
	 * @param array  result  分页用的数组
	 * @param string $listvar   赋给模板遍历的变量名 默认list
	 * @param string $template ajaxlist的模板名
	 * @param unknown $param
	 */
	protected function _cjax_page($param){
		$this->assign($param['listvar'], $param['result']);
		$this->assign('count',  $param['count']);
		if(IS_POST){
			layout(false);
			$this->success($this->fetch($param['template']), $param['count']);
		}
	}
	
	private function check_access($roleid){
		// 暂时放开权限，以后再限制
		return true;
		
		//如果用户角色是1，则无需判断
		if($roleid == 1){
			return true;
		}
		$role = D("Role")->field("status")->where("id=$roleid")->find();
		
		if(!empty($role) && $role['status']==1){
			$group = MODULE_NAME;
			$model = CONTROLLER_NAME;
			$action = ACTION_NAME;
			if(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME != "AdminIndexindex"){
				$count = D("Access")->where ( "role_id=$roleid and g='$group' and m='$model' and a='$action'")->count();
				return $count;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	
	/**
     * 推送消息指定的用户
     * @param $userID  用户ID
     * @param $title   消息的标题
     * @param $content 消息的内容
     * @param $push_type  5-批发采购活动结束并成功返现  6-晒单成功  7-晒单审核不通过
     * @param $message_type: 0-文本消息  1-图文消息
     * @param $imageurl   图文消息的图片相对路径
     * @param $linkurl    消息跳转URL
     */
	protected function push($user_id,$title,$content,$push_type = null,$message_type = 0,$imageurl = null,$linkurl = null,$linktype = 0,$linkid = null){
		$this->pushByGuid($user_id,$title,$content,null,$push_type,$message_type,$imageurl,$linktype,$linkurl,$linkid);
	}

	// 为了不影响原接口调用：新增推送消息方法
	protected function pushByGuid($userId,$title,$content,$msgid = null,$push_type = null,$message_type = 0,$imageurl = null,$linktype = 0,$linkurl = null,$linkid = null){
		//通知APP用户
		$superUserToken = $this->getSuperUserToken();  // 获取超级用户的用户凭证
		$noticeData['msgid'] = empty($msgid)? guid() : $msgid;
		$noticeData['usertoken'] = $superUserToken? $superUserToken : '-1';
		$noticeData['toid'] = $userId;
		$noticeData['totype'] = 1;
		$noticeData['title'] = $title;
		$noticeData['content'] = $content;
		$noticeData['messagetype'] = $message_type;
		if ($push_type) $noticeData['sys_push_type'] = $push_type;
		if ($message_type == 1) $noticeData['imageurl'] = !empty($imageurl)? __IMG_URL__ . $imageurl : '';  // 图文消息
		if (is_numeric($linktype)) {
			$noticeData['linktype'] = $linktype;  // 跳转类型：0-不跳转  1-web页面  2-店铺  3-药品详情  4-我的钱  5-订单详情
			if ($linktype == 1) $noticeData['linkurl'] = $linkurl;
			elseif(in_array($linktype, array(2,3,5))) $noticeData['linkid'] = $linkid;
		}
		$tmpInfo = pushMsg($noticeData);
		return $tmpInfo;
	}

	//实名认证通过后，通知APP(通过推送通知)
	protected function pushAuthMsg($userID){
		//通知APP用户
		$superUserToken = $this->getSuperUserToken();  // 获取超级用户的用户凭证
		$msg['authcode'] = '123456';
		$msg['format'] = 'json';
		$msg['usertoken'] = $superUserToken? $superUserToken : '-1';
		$msg['real-name-userid'] = $userID;
		$curl = new p\Curl();
		$url = __JAVA_URL__ . 'ysb/servlet/messages/realname/bound';   // 消息推送接口地址
		$tmpInfo = $curl->post($url, json_encode($msg),1);
		return $tmpInfo;
	}
	
	/**
	 * 检查用户是否实名认证
	 * @param int $user_id 用户id
	 * $key   是否实名失败的检查
	 */
	protected function checkUserRealNameAuthentication($user_id,$key = false){
		$status = D('UserAuthentication')->getAuthention($user_id);
		if($key){
			if($status == \Admin\Model\UserAuthenticationModel::REAL_NAME_UNDER_VERIFY){
				$this->error('亲，用户还在实名认证中，不能提现哦！');
			}
		}else{
			if($status != \Admin\Model\UserAuthenticationModel::REAL_NAME_VERIFY_SUCCESS){
				$this->error('亲，用户还没实名认证，不能提现哦！');
			}
		}
	}
	
	/**
	 * 检查是否是采购角色或者admin或者是否是供应商
	 * $auth 传参数不报错
	 */
	protected function checkCaigouAuth($auth=null){
		
		$role_id = $this->_account['role_id'];
		$this->assign('role_id',$role_id);
		
		if($role_id == 1 || $role_id == 12){ //采购或者admin
			$caigouKey = 1;
			$this->assign('caigouKey',$caigouKey);   //识别采购和amdin
			
			if($role_id == 12){
				$this->assign('caigouRole',1);
			}				
			return array();
		}else{
			$providerInfo = D('RoleProvider')->checkProvider($role_id); //检查是否是供应商			
			if($providerInfo){
				$providerKey = 1;
				$this->assign('providerKey',$providerKey);
				$this->assign('providerInfo',$providerInfo);
				return $providerInfo;
			}else{
				if(!$auth){ //传此参数不报错
					$this->error('亲，没有权限哦！');
				}
			}
		}
		
	}
	
	/**
	 * 对比下单时间是否大于20150616号
	 * @param int $time 下单时间  修改库存用到
	 */
	protected function checkTime($time){
		$compareTime = '1434383999';
		if($time > $compareTime){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 清掉缓存中信息(一般调用JAVA接口)
	 * @param $cacheName  如LCSysConfig
	 * @param $key
	 * @param $admin      ysb - 店员版       ysbc - 大众版
	 * @param $debug      true - 输出调用结果    false - 不输出调用结果
	 */
    public function resetCacheByName($cacheName, $key = null, $admin = 'ysb', $debug = true){
        $curl = new p\Curl();
        if ($admin == 'ysb') {  // 店员版
        	$url = __JAVA_URL__ . 'ysb/servlet/internal/cache/admin/resetAllServers?cacheName=' . $cacheName;
        	if ($key) $url .= "&key=" . $key;
        }else{  // 大众版
        	$url = __JAVA_URL_PUB__ . 'public-ysb/servlet/internal/cache/admin/resetAllServers?cacheName=' . $cacheName;
        }
        
        $result = $curl->get($url);
        if ($debug) $this->_log('[清掉缓存中信息]:[' . $url . ']' . $result);
        return $result;
    }

    /**
     * 获取不用图片的url
     */
    protected function getImageBaseUrl(){
    	if(__ENV_FLAG__ == 'DEV_SERVER'){
    		return __BASE__;
    	}elseif(__ENV_FLAG__ == 'TEST_SERVER_ONLINE'){
    		return 'http://test.ysbang.cn/';
    	}else{
    		return 'http://img.ysbang.cn/';
    	}
    }
    
    // 更新Solr中保存的药品数据
    protected function resetSolrDrugInfoData($debug = false){
        $envFlag = defined('__ENV_FLAG__')? __ENV_FLAG__ : 'PRODUCTION_SERVER';        // 环境标志
        if ($envFlag == 'DEV_SERVER') {  // 开发环境
            $url = 'http://192.168.0.9:8983/solr/ysb_dict_druginfo_shard1_0_replica1/dataimport?command=full-import&wt=json';
        }elseif ($envFlag == 'PRODUCTION_SERVER') {  // 生产环境
            $url = 'http://192.168.0.34:8983/solr/ysb_dict_druginfo_shard1_replica1/dataimport?command=full-import&wt=json';
        }else{  // 测试环境
            $url = 'http://192.168.0.42:8983/solr/ysb_dict_druginfo_shard1_replica1/dataimport?command=full-import&wt=json';
        }
        $curl = new p\Curl();
        $result = $curl->get($url);
        if ($debug) $this->_log('[更新Solr药品数据]:' . $result);
    }

    // 更新Solr中保存的采购活动数据
    protected function resetSolrWholesaleDrugData($debug = false){
        $envFlag = defined('__ENV_FLAG__')? __ENV_FLAG__ : 'PRODUCTION_SERVER';        // 环境标志
        if ($envFlag == 'DEV_SERVER') {  // 开发环境
            $url = 'http://192.168.0.9:8983/solr/wholesale_drug_shard1_replica2/dataimport?command=full-import&clean=true&commit=false&wt=json';
        }elseif ($envFlag == 'PRODUCTION_SERVER') {  // 生产环境
            $url = 'http://192.168.0.34:8983/solr/wholesale_drug_shard1_replica2/dataimport?command=full-import&clean=true&commit=false&wt=json';
        }else{  // 测试环境
            $url = 'http://192.168.0.42:8983/solr/wholesale_drug_shard1_replica2/dataimport?command=full-import&clean=true&commit=false&wt=json';
        }
        $curl = new p\Curl();
        $result = $curl->get($url);
        if ($debug) $this->_log('[更新Solr采购活动数据]:' . $result);
    }

    // 更新Solr中保存的课程信息(课程搜索)
    protected function resetSolrCourseData($debug = false){
        $envFlag = defined('__ENV_FLAG__')? __ENV_FLAG__ : 'PRODUCTION_SERVER';        // 环境标志
        if ($envFlag == 'DEV_SERVER') {  // 开发环境
            $url = 'http://192.168.0.9:8983/solr/course_video/dataimport?command=full-import&wt=json';
        }elseif ($envFlag == 'PRODUCTION_SERVER') {  // 生产环境
            $url = 'http://192.168.0.34:8983/solr/course_video/dataimport?command=full-import&wt=json';
        }else{  // 测试环境
            $url = 'http://192.168.0.42:8983/solr/course_video/dataimport?command=full-import&wt=json';
        }
        $curl = new p\Curl();
        $result = $curl->get($url);
        if ($debug) $this->_log('[更新Solr课程信息]:' . $result);
    }

    /**
	 * 按json格式输出通信数据
	 * @param integer $code 状态码
	 * @param string $message 提示信息
	 * @param array $data 数据
	 * @return string
	 */
	protected function showJson($code, $message = '', $data = array()) {
		$result = array(
				'code' => $code,
				'message' => $message,
				'data' => $data
		);
		echo json_encode($result);
		exit;
	}
}