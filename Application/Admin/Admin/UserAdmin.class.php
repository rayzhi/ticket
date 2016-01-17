<?php

namespace Admin\Admin;
use \Common\Lib\Pclass as p;

class UserAdmin extends CommonAdmin {
	
    public function indexAct(){
    	$list = D('AdminAccount')->get_all_account();
    	$this->assign('list',  $list);
    	$this->display();
    }

    public function account_addAct(){
    	if(IS_POST){
    		if (D("AdminAccount")->create()) {
    			D("AdminAccount")->adduid = $this->_account['uid'];
    			$insert_id = D("AdminAccount")->add();
    			if ($insert_id !== false) {
    				$this->_admin_log($insert_id, '添加管理员',"成功！");
    				$this->success("添加成功",UC("User/index"));
    			} else {
    				$this->_admin_log(0, '添加管理员',"失败！");
    				$this->error("添加失败！",UC("User/index"));
    			}
    		} else {
    			$this->_admin_log(0, '添加管理员实例化时',"失败！");
    			$this->error(D("AdminAccount")->getError());
    		}
    	}else{
    		$role_list = D('Role')->simple_role_list();

    		$this->assign("headline",  "新增管理员");
    		$this->assign("role_list", $role_list);
    		$this->assign("action_name", ACTION_NAME);
    		$this->display("User:account_oper");
    	}
    }
    
    /**
     * 编辑管理员
     */
    public function account_editAct() {
    	if (IS_POST) {
    		$id = intval(I("post.uid"));
    		if(empty($_POST['password'])){
    			unset($_POST['password']);
    		}
    		$data = D("AdminAccount")->create();
    		if ($data) {
                if (empty($data['password'])) unset($data['password']);
    			if (D("AdminAccount")->save($data)!== false) {
    				$this->_admin_log($id, '修改管理员',"成功！");
    				$this->success("修改成功！", UC('User/index'));
    			} else {
    				$this->_admin_log($id, '修改管理员',"失败！");
    				$this->error("修改失败！".D("AdminAccount")->getlastsql().print_r($data, true));
    			}
    		} else {
    			$this->_admin_log($id, '修改管理员',"失败！".D("AdminAccount")->getError());
    			$this->error(D("AdminAccount")->getError());
    		}
    	}else{
    		$uid = intval(I("get.uid"));
    		if (!$uid) {
    			$this->error("非法操作！");
    		}
    		$role_id = D('AdminAccount')->where(array('uid' => $uid))->getField('role_id');
    		if($role_id == 1){
    			$this->error("超级管理员不能编辑！", UC('User/index'));
    		}
    		$data = D("AdminAccount")->field('uid, account, role_id')->where(array("uid" => $uid))->find();
    		if (!$data) {
    			$this->error("编辑项不存在！");
    		}
    		
    		$role_list = D('Role')->simple_role_list($data['role_id']);
    		$this->assign("headline", "编辑管理员");
    		$this->assign("role_list", $role_list);
    		$this->assign("action_name", ACTION_NAME);
    		$this->assign("info",  $data);
    		$this->display("User:account_oper");
    	}
    }
    
    /**
     * 删除礼品选项
     */
    public function del_accountAct() {
    	$id = I("post.uid");
    	if(!$id){
    		$this->error("删除项不存在！", UC('User/index'));
    	}
    	$role_id = D('AdminAccount')->where(array('uid' => $id))->getField('role_id');
    	if($role_id == 1){
    		$this->error("超级管理员不能删除！", UC('User/index'));
    	}
    	$status = D("AdminAccount")->delete($id);
    	if ($status !== false) {
    		$this->_admin_log($id, '删除管理员',"成功！");
    		$this->success("删除成功！", UC('User/index'));
    	} else {
    		$this->_admin_log($id, '删除管理员',"失败！");
    		$this->error("删除失败！", UC('User/index'));
    	}
    }
    
    /**
     * 用户管理列表
     */
    public function userAct(){

    	$this->_set_page();
    	$title = I('post.title');
    	$startday = I('post.startDay');
    	$endday = I('post.endDay');
    	$address = I('post.address');
    	$drugName = I('post.drugName');
        $appUserFlag = I('post.appUserFlag');
        $join_store_type = I('post.join_store_type');  // 0-全部  1-已加入药店  2-未加入药店
        $province = I('post.province');
        $city = I('post.city');
        $district = I('post.district');

    	$list = D('User')->get_user_info($this->_page, $this->_pagesize, $title,$startday,$endday,$address,$drugName,$appUserFlag,$join_store_type,$province,$city,$district);
    	$count = $list['count'];
    
    	$param = array(
    			'result' => $list['data'],                 //分页用的数组
    			'count' => $count,                 //数组的量
    			'listvar' => 'list',                 //分页循环变量
    			'template' => 'Libfetch:userlib'  //ajax更新模板
    	);
    
    	$this->_cjax_page($param);
    
  
    	$other = 'startDay:$("input[name=startDay]").val(),endDay:$("input[name=endDay]").val(),drugName:$("input[name=drugName]").val(),
    			  address:$("input[name=address]").val(),appUserFlag:$("input[name=appUserFlag]").val(),join_store_type:$("#join_store_type option:selected").val(),
                  province:$("#province option:selected").val(),city:$("#city option:selected").val(),district:$("#district option:selected").val()';
    	
    	//表单顶部html以及底部html
    	$top_html = cjax_table_top(array('field_name' => 'title', 'field_title' => '昵称/姓名/手机号/用户ID'));
    	$bottom_html = cjax_table_bottom(array('count' => $count, 'field_name' => 'title','other' => $other));
    	$this->assign('top_html', $top_html);
    	$this->assign('bottom_html', $bottom_html);
    	
    	//昨天新增人数
    	$yesterdayNum = D('User')->getYesterdayNum();
    	
    	//今天新增人数
    	$todayNum = D('User')->getTodayNum();

        // 药师帮app用户数
        $appUserNum = D('User')->getAppUserNum();
    	
    	//总人数
    	$this->assign('totalUser', $count);
    	$this->assign('yesterdayNum', $yesterdayNum);
    	$this->assign('todayNum', $todayNum);
        $this->assign('appUserNum', $appUserNum);

        //所有省份
        $province = D('Areas')->getProvince();
        $this->assign("province",$province);
    
    	$this->display();
    }
    
    /**
     * 用户详情页
     *
     */
    public function moreUserInfoAct(){
    
     	$user_id = I('get.id');
     	if(!$user_id){
     		$this->error('非法操作！');
    	}
    	
     	$user = D('User')->where(array("id"=>$user_id))->find();
     	$userOtherinfo = D('UserOtherinfo')->getByUserId($user_id);
    	$userAuthentication = D('UserAuthentication')->get_one_data($user_id);
    	$userAddress = D('UserAddress')->get_one_data($user_id);
    	$userPoint   = D('UserPoint')->get_one_data($user_id);
    	$userAccount = D('UserAccount')->get_one_data($user_id);
    	$userCoupon  = D('UserCoupon')->get_coupon_data($user_id);
    	$count = count($userCoupon);
    	
     	$userOtherinfo['headurl'] = $userOtherinfo['head_url']?__IMG_URL__.$userOtherinfo['head_url']:__BASE__.'/public/static/admin/img/default.jpg';
    	//手机归属地
    	if($user['phone']){
     		$user['phonePlace'] = D('PhoneArea')->getPhonePlace($user['phone']);
    	}
    	
    	//所在药店
    	$store = D('UserDrugstorebranch')->getJoinStoreInfoByUid($user_id);
    	$user['store'] = $store['storetitle'];
    	$user['storeAddress'] = $store['address'];
        $userOtherinfo['work'] = $store['position_desc'];  // 职务

    	//注册时间
    	if($user['ctime']){
    		$user['ctime'] = date('Y-m-d H:i:s',$user['ctime']);
    	}else{
    		$user['ctime'] = '';
    	}

    	//最后登陆时间
        $user['ltime'] = !empty($userOtherinfo['last_login_time'])? date('Y-m-d H:i:s',$userOtherinfo['last_login_time']) : '';

    	//默认显示积分的数据
    	$data = D('PointLog')->getDetailPoint($user_id);
    	$this->assign('list1', $data);

    	//扫码数量
    	$scanNum = D('DrugInwarehouse')->getUserScanNum($user_id);
    	$this->assign('scanNum', $scanNum);

    	//晒单数量
    	$shareNum = D('DrugOutwarehouse')->getUserShareNum($user_id);
    	$this->assign('shareNum', $shareNum);

    	//优惠券数量
    	$couponNum = D('UserCoupon')->get_coupon_data($user_id);
    	$this->assign('couponNum', $couponNum);

    	//抢单数量
    	//$sendNum = D('YsbcOrder')->getSendOrderNum($user_id);
        $sendNum = 0;
    	$this->assign('sendNum', $sendNum);

    	//采购数量
    	$wholesaleNum = D('WholesaleOrder')->getMyOrderNum($user_id);
    	$this->assign('wholesaleNum', $wholesaleNum);
 
        //历史药店
        $historyStoreNum = D('UserDrugstorebranch')->getHistoryStoreByUid($user_id);
        $this->assign('historyStoreNum', $historyStoreNum);
		
        $this->assign('user_id', $user_id);
     	$this->assign('user', $user);
     	$this->assign('userOtherinfo', $userOtherinfo);
    	$this->assign('userAddress', $userAddress);
    	$this->assign('userAuthentication', $userAuthentication);
    	$this->assign('userPoint', $userPoint);
    	$this->assign('userAccount', $userAccount);
    	$this->assign('count', $count);
    	
        // 是否已加入药店
        $storeData = D("UserDrugstorebranch")->getUserDrugStoreDataByCondition($user_id, 1);
        if (!empty($storeData)) $canChange = 1;
        else $canChange = 0;
        
        $this->assign('canChange', $canChange);
    	
    	$this->display();
    }
    
    /**
     * 我的金额
     */
    public function cashAct(){
    	$userid = I('post.id');

    	if(!$userid){
    		$this->error('用户id丢失');
    	}  
    	$data = D('AccountslideLog')->getDetailAccount($userid);
  
    	$this->assign('list', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:cashlib'));
    
    }
    
    /**
     * 冻结金额
     */
    public function blockAct(){
    	$userid = I('get.id');

    	if (IS_POST) {
    		$id = intval(I("post.id"));
    		$userid = intval(I("post.userid"));
            $data = D('UserAccount')->create();
            if ($data) {
                if (D('UserAccount')->save($data) !== false) {
                    $this->success("修改成功！", UC("User/cash?id=$userid"));
                } else {
                    $this->error("修改失败！" . D('UserAccount')->getlastsql() . print_r($data, true));
                }
            } else {
                $this->error(D('UserAccount')->getError());
            }
    	} else {
    		$info = D('UserAccount')->get_one_data($userid);
    		$type = D('UserAccount')->get_status();
    		$this->assign("headline", "是否冻结");
    		$this->assign("action_name", ACTION_NAME);
    		$this->assign("userid", $userid);
    		$this->assign("id", $info['id']);
    		$this->assign("type", $type);
    	}
    	 
    	$this->display();
    }
    
    /**
     * 我的积分
     */
    public function pointAct(){
    	$userid = I('post.id');
    
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    	 
    	$data = D('PointLog')->getDetailPoint($userid);
    	 
    	$this->assign('list1', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:detailPointlib'));
    
    }
    
    /**
     * 冻结积分
     */
    public function block_pointAct(){
    	$userid = I('get.id');
    
    	if (IS_POST) {
    		$id = intval(I("post.id"));
    		$userid = intval(I("post.userid"));
    		$data = D('UserPoint')->create();
    		if ($data) {
    			if (D('UserPoint')->save($data) !== false) {
    				$this->success("修改成功！", UC("User/point?id=$userid"));
    			} else {
    				$this->error("修改失败！" . D('UserPoint')->getlastsql() . print_r($data, true));
    			}
    		} else {
    			$this->error(D('UserPoint')->getError());
    		}
    	} else {
    		$info = D('UserPoint')->get_one_data($userid);
    		$type = D('UserPoint')->get_status();
    		$this->assign("headline", "是否冻结");
    		$this->assign("action_name", ACTION_NAME);
    		$this->assign("userid", $userid);
    		$this->assign("id", $info['id']);
    		$this->assign("type", $type);
    	}
    
    	$this->display();
    }
    
    /**
     * 我的优惠券
     */
    public function couponAct(){

    	$userid = I('post.id');
    	
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    	
    	$data = D('UserCoupon')->get_coupon($userid);    	
    	
    	$this->assign('list5', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:couponlib'));
    
    }
    
    /**
     * 积分规则
     */
    public function point_ruleAct() {
    	$data = D("PointCalcrule")->dataList();
    	$this->assign("list", $data);
    	$this->display();
    }
    
    /**
     * 添加积分规则
     */
    public function add_point_ruleAct() {
    	if (IS_POST) {
    		if (D("PointCalcrule")->create()) {
    			$id = D("PointCalcrule")->add();
    			if ($id !== false) {
    				$this->_admin_log($id, '添加积分规则User',"成功！");
    				$this->success("添加成功",UC("User/point_rule"));
    			} else {
    				$this->error("添加失败！",UC("User/point_rule"));
    			}
    		} else {
    			$this->error(D("PointCalcrule")->getError());
    		}
    	} else {
    
    		$this->assign("headline", "添加积分规则");
    		$this->assign("action_name", ACTION_NAME);
    		$this->display();
    	}
    }
    
    /**
     * 编辑积分规则
     */
    public function edit_point_ruleAct() {
    	if (IS_POST) {
    		$data = D("PointCalcrule")->create();
    		if ($data) {
    			if (D("PointCalcrule")->save($data)!== false) {
    				$this->_admin_log($data['id'], '编辑积分规则User',"成功！");
    				$this->success("修改成功！", UC("User/point_rule"));
    			} else {
    				$this->error("修改失败！".D("PointCalcrule")->getlastsql().print_r($data, true));
    			}
    		} else {
    			$this->error(D("PointCalcrule")->getError());
    		}
    	}else{
    		$id = intval(I("get.id"));
    		if (!$id) {
    			$this->error("非法操作！");
    		}
    		$data = D("PointCalcrule")->where(array("id" => $id))->find();
    		if (!$data) {
    			$this->error("该积分规则不存在！");
    		}
    		$this->assign("headline", "编辑积分规则");
    		$this->assign("action_name", ACTION_NAME);
    		$this->assign("info",  $data);
    		$this->display("User:add_point_rule");
    	}
    }
    
    /**
     * 删除积分规则 只能单个删除
     */
    public function del_point_ruleAct() {
    	$id = intval(I("post.id"));
    
    	$status = D("PointCalcrule")->delete($id);
    	if ($status !== false) {
    		$this->_admin_log($id, '删除积分规则User',"成功！");
    		$this->success("删除成功！", UC("User/point_rule"));
    	} else {
    		$this->error("删除失败！");
    	}
    	 
    }
    
    /**
     * 我的扫码
     */
    public function scanAct(){
    	$userid = I('post.id');
    
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    
    	$data = D('DrugInwarehouse')->getUserScan($userid);
    
    	$this->assign('list2', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:scanlib'));
    
    }
    
    /**
     * 我的晒单
     */
    public function shareAct(){
    	$userid = I('post.id');
    
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    
    	$data = D('DrugOutwarehouse')->getUserShare($userid);
    
    	$this->assign('list4', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:sharelib'));
    
    }
    
    /**
     * 我的抢单
     */
    public function sendorderAct(){
        $this->error('抢单功能已屏蔽！');
    	$userid = I('post.id');
    	 
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    	
    	$data = D('YsbcOrder')->getMyOrder($userid);
    	$this->assign('list6', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:sendlib'));
    
    }
    
    /**
     * 我的采购
     */
    public function wholesaleAct(){
    
    	$userid = I('post.id');
    
    	if(!$userid){
    		$this->error('用户id丢失');
    	}
    	 
    	$data = D('WholesaleOrder')->myWholesaleOrder($userid);
    	
    	$this->assign('list7', $data);
    	layout(false);
    	$this->success($this->fetch('Libfetch:myWholesalelib'));
    
    }

    /**
     * 我的历史药店
     */
    public function historyStoreAct(){
        $userId = I('post.id');

        if (!$userId) {
            $this->error('用户id丢失');
        }

        $data = D('UserDrugstorebranch')->getLeaveStoreInfoByUid($userId);

        foreach ($data as $k => $v) {
            $branchId = $v['drugstore_branch_id'];
            $branchInfo = D('DrugstoreBranch')->getDrugstoreById($branchId);
            $data[$k]['storetitle'] = $branchInfo['storetitle'];        //药店名称

            $applyTime = $v['apply_time'];
            $leaveTime = $v['leave_time'];

            $data[$k]['apply_time']     = date('Y年m月d日', $applyTime);   //加入药店时间
            $data[$k]['leave_time']     = date('Y年m月d日', $leaveTime);   //离开药店时间
            $data[$k]['scanNum']        = D('DrugInwarehouse')->getUserScanNum($userId, $applyTime, $leaveTime);    //扫码次数
            $data[$k]['shareNum']       = D('DrugOutwarehouse')->getUserShareNum($userId, $applyTime, $leaveTime); //晒单次数
            $data[$k]['wholesaleNum']   = D('WholesaleOrder')->getMyOrderNum($userId, $applyTime, $leaveTime); //采购次数
        }

        $this->assign('list8', $data);
        layout(false);
        $this->success($this->fetch('Libfetch:historyStorelib'));

    }

    /**
     * 用户注册
     */
    public function registerByAdminAct() {
        if (IS_POST) {
            $phoneNum = I('post.phonenum');
            $realName = I('post.real_name');
            if (!$phoneNum) $this->error("手机号码不能为空！");
            if (!is_numeric($phoneNum) || strlen($phoneNum) != 11) $this->error("手机号码输入错误！");

            $joinstore = I('post.joinstore');  // 是否加入药店：1-否  2-是
            $storeid = I('post.drugstore');    // 药店ID

            if ($joinstore == 2) {  // 选择加入药店
                if (empty($storeid)) $this->error("请选择药店！");
                $storeData = D('DrugstoreBranch')->getDrugstoreById($storeid);
                if (empty($storeData)) $this->error("选择药店不存在！");
            }

            $isJoin = ($joinstore == 2)? true : false;  // 标记是否加入药店：false-否  true-是

            // 根据手机号码判断用户是否已经注册
            $userInfo = D('User')->getUserInfoByPhone($phoneNum);
            if ($userInfo && $userInfo['is_bindphone'] == 1) $this->error("该手机号码已经注册！");

            $password = $phoneNum;  // 初始密码是手机号码
            
            /**
             * 注册成功后需要插入的表：
             *     用户表、用户其他信息表、积分表
             *     用户认证信息表、用户账户信息
             */
            $userName = $this->newGenerateUserName($phoneNum);  // 生成用户名
            $loginSalt = rand(11111,99999);   // md5加密盐值

            $amount = 0;  // 初始化金额
            //$phoneData = D('Specialuser')->getSpecialUserInfoByPhone($phoneNum);
            //if (!empty($phoneData)) $amount = 10;  // 送10块金额

            $newUserId = $newUserOtherId = $newUserAuthId = $newUserAccountId = $newUserPointId = $updateLogResult = $joinStoreResult = true;
            // 使用事务
            $userModel = D('User');
            $logModel = D('AccountslideLog');
            $storeModel = D('UserDrugstorebranch');
            $userModel->startTrans();  // 开始事务
            $logModel->startTrans();
            $storeModel->startTrans();

            // 添加新用户
            $newUserId = $userModel->addUser($phoneNum,$password,$loginSalt,1);

            // 添加用户其他信息
            $newUserOtherId = D('UserOtherinfo')->addUserOtherInfo($newUserId,$userName);

            // 添加用户认证信息
            $newUserAuthId = D('UserAuthentication')->addUserAuthInfo($newUserId,$realName);

            // 增加用户账户信息
            $newUserAccountId = D('UserAccount')->addUserAccountInfo($newUserId, $amount);

            // 增加用户积分信息
            $newUserPointId = D('UserPoint')->addUserPointInfo($newUserId);

            if ($amount > 0) {
                // 记录账户变动流水(无订单号则填-1)
                $updateLogResult = $logModel->addAccountslideLogInfo($newUserId, 1, 0, $amount, 15, -1, '注册赠送现金');
            }

            if ($isJoin) {  // 帮助用户加入药店
                $joinStoreResult = $storeModel->joinStoreByUid($newUserId, $storeid);
            }
            
            if ($newUserId !== false && $newUserOtherId !== false && $newUserAuthId !== false && $newUserAccountId !== false 
                && $newUserPointId !== false && $updateLogResult !== false && $joinStoreResult !== false) {
                $userModel->commit();    // 提交
                $logModel->commit();
                $storeModel->commit();

                // 添加用户登录帐号
                $updateAccResult = D('User')->updateUserLoginAccount($newUserId);

                // 用户注册可获得积分(增加用户积分)
                /*
                $changePoint = 5;   // 变动积分额(增加)
                $updateResult = $this->changeUserPoint($newUserId, $changePoint, 1, 0);
                */

                // 推送消息
                if ($amount > 0) {
                    $title = '欢迎注册使用药师帮！';
                    $content = '亲爱的用户：欢迎注册使用药师帮！'. $amount .'元现金已经送达您的账户，点击“药财富”可查看。药师帮，帮药师，药师帮致力于用移动互联网为药店和药师服务：提供移动医药培训课堂，帮助广大药师通过执业药师资格考试；构建移动医药商务平台，帮助广大药师实现专业提升、薪酬提升和社会地位提升。如有疑问，欢迎来电咨询4009222115。';
                    $this->push($newUserId, $title, $content);
                }

                // 发送短信
                $params['param1'] = $phoneNum;
                $params['param2'] = $password;
                p\SMSService::sendSmsContent($phoneNum, '30308', $params);
                $this->success("注册成功！",UC("User/user"));
            }else{
                $userModel->rollback();  // 回滚
                $logModel->rollback();
                $storeModel->rollback();
                $this->error("注册失败！");
            }           
        }else{
            $this->assign("headline", "后台手工添加药师帮用户");
            $this->assign("action_name", ACTION_NAME);

            //所有省份
            $province = D('Areas')->getProvince();
            $this->assign("province",$province);
            $this->display();
        }
    }

    /**
     * 注册时生成用户的昵称(新方法)
     * @param $nameSuffix 用户名后缀
     * @return String 用户的昵称
     */
    private function newGenerateUserName($nameSuffix){
        $zero = '00000';
        $minLen = strlen($zero);  // 最少5位
        $nameStr = strval($nameSuffix);
        $nameLen = strlen($nameStr);
        if ($nameLen < $minLen) {
            $nameStr = substr($zero, $nameLen, $minLen - $nameLen) . $nameStr;
        }else{
            $nameStr = substr($nameStr, -$minLen);
        }
        $userName = '药师' . $nameStr;
        return $userName;
    }

    /**
     * 改变(增加、或减少)用户的积分：修改用户积分、记录积分日志
     * @param $userId       用户ID
     * @param $changePoint  变动积分额
     * @param $type    类型：0-减少  1-增加
     * @param $optType 操作类型：0-注册  1-实名认证  2-药师认证  3-完善个人资料  4-登录(每天首次) 
     *                           5-评论(每天首次)    6-购买  7-分享(前三次不同课程分享)
     *                           8-购买积分商城物品
     * @return true:修改成功 flase:修改失败
     */
    protected function changeUserPoint($userId, $changePoint, $type, $optType){
        $updateResult = false;
        if ($type == 1) {  // 增加
            $updateResult = D('UserPoint')->incUserPoint($userId,$changePoint);
        }else{  // 减少
            $updateResult = D('UserPoint')->decUserPoint($userId,$changePoint);
        }
        
        // 记录用户积分变动
        $userPointData = D('UserPoint')->getUserPointInfoByUid($userId);  // 获取用户积分信息
        $remainPoint = $userPointData['avail_point'];      // 变动后积分额

        // 变动前积分总额
        $prePoint = 0;
        if ($type == 1) {  // 增加
            $prePoint = $remainPoint - $changePoint;
        }else{
            $prePoint = $remainPoint + $changePoint;
        }
        
        // 记录送礼品日志
        $newPointLogId = D('PointLog')->addPointLogInfo($userId, $prePoint, $changePoint, $remainPoint, $type, $optType);
        $result = false;
        if ($updateResult || $newPointLogId) $result = true;  // 修改用户积分、或添加用户积分日志成功都返回true
        return $result;
    }
    
    /**
     * 用户资金明细
     */
    public function userFundsAct(){
    
    	$this->_set_page();
    	$title = I('post.title');
    
    	$list = D('User')->getUserFunds($this->_page, $this->_pagesize, $title);
    	$count = $list['count'];
    	
    	$param = array(
    			'result' => $list['data'],                 //分页用的数组
    			'count' => $count,                 //数组的量
    			'listvar' => 'list',                 //分页循环变量
    			'template' => 'Libfetch:userFundslib'  //ajax更新模板
    	);
    
    	$this->_cjax_page($param);
    
    	//表单顶部html以及底部html
    	$top_html = cjax_table_top(array('field_name' => 'title', 'field_title' => '昵称/姓名/手机号'));
    	$bottom_html = cjax_table_bottom(array('count' => $count, 'field_name' => 'title'));
    	$this->assign('top_html', $top_html);
    	$this->assign('bottom_html', $bottom_html);
    	
    	$chargeAmount = D('AccountslideLog')->getUserIncomeCash();
    	$scanAmount = D('DrugInwarehouse')->getUserScanTotalAmount();
    	$shareOrderAmount = D('DrugOutwarehouse')->getUserShareorderTotalAmount();
    	$withdrawAmount = D('WithdrawLog')->getUserWithdrawTotalAmount();
    	$wholesaleAmount = D('WholesaleOrder')->getUserWholesaleTotalAmount();
    	
    	$this->assign('chargeAmount', $chargeAmount);
    	$this->assign('scanAmount', $scanAmount);
    	$this->assign('shareOrderAmount', $shareOrderAmount);
    	$this->assign('withdrawAmount', $withdrawAmount);
    	$this->assign('wholesaleAmount', $wholesaleAmount);
    	 
    	$this->display();
    }

    /************************************************************************/
    // 三九项目
    public function sanJiuUserAct(){
        $this->_set_page();
        $title = I('post.title');
        $optype = I('get.optype')? I('get.optype') : I('post.optype');
        $list = D('VideoUserList')->getSanJiuUserData($this->_page, $this->_pagesize, $title, $optype);

        $count = $list['count'];
        $param = array(
                'result'=>$list['data'],             //分页用的数组
                'count'=>$count,             //数组的量
                'listvar'=>'list',           //分页循环变量
                'template'=>'Libfetch:sanJiuUserlib' //ajax更新模板
        );
        $this->_cjax_page($param);
        $other = 'optype:$("input:radio[name=\'optime-radio\']:checked").val()';
        //表单顶部html以及底部html
        $top_html = cjax_table_top(array('field_name'=>'title', 'field_title'=>'姓名/手机号'));
        $bottom_html = cjax_table_bottom(array('count'=>$count, 'field_name'=>'title', 'other' => $other));
        $this->assign('top_html', $top_html);
        $this->assign('bottom_html', $bottom_html);
        $this->assign('optype', $optype);
        $this->display();
    }

    public function addSanJiuUserAct(){
        if(IS_POST){
            $data = $_POST;
            $register = $data['register'];  // 导入类型：0-只导入  1-导入并注册

            // 设置php临时处理时间
            ini_set('max_execution_time', '180');  // 设置为3分钟

            if (!empty($_FILES['xls_file']) && $_FILES['xls_file']['size'] > 0) {  // 从Excel文件批量导入三九用户
                $phoneNums = array();
                $_upload_file       = $_FILES['xls_file']['tmp_name'];
                $_upload_file_name  = $_FILES['xls_file']['name'];
                $_file_extension    = strtolower(pathinfo( $_upload_file_name,PATHINFO_EXTENSION));// 得知这个附件的文件类型
                    
                if ($_file_extension != "xlsx") $this->error("请上传扩展名为 xlsx 的EXCEL2007文件。");
                
                Vendor('PHPExcel.IOFactory');
                Vendor('PHPExcel.PHPExcel');        
                $PHPExcel_IOFactory = new \PHPExcel_IOFactory();
                    
                $inputFileType = 'Excel2007';
                $objReader =  $PHPExcel_IOFactory->createReader($inputFileType);
        
                $objPHPExcel = $objReader->load($_upload_file);
                $objPHPExcel->setActiveSheetIndex(0);
                $objSheet = $objPHPExcel->getActiveSheet();
                $_exam_title = $objSheet->getTitle();
                $highestRow = $objSheet->getHighestRow();           //取得总行数
                
                /************************* 导入逻辑开始     ***************************/
                $importNums = 0;  // 导入数
                $importSuccNums = 0;  // 导入成功数
                $registerSuccNums = 0;  // 注册成功数
                $i = 1;
                $importData = array();  // 保存导入的数据
                while($i <= $highestRow)
                {
                    $i++;  // 继续下一行取值(从第二行开始)
                    unset($tmp);
                    //取出excel整行数据
                    $tmp['address'] = $objSheet->getCell('A'.$i)->getValue();  // 省份
                    $tmp['uname'] = $objSheet->getCell('B'.$i)->getValue();  // 姓名
                    $tmp['phone'] = $objSheet->getCell('C'.$i)->getValue();  // 手机号

                    if (empty($tmp['phone']) || !is_numeric($tmp['phone']) || strlen($tmp['phone']) != 11) continue;  //手机号码不能为空等

                    // 判断之前是否已经导入
                    $sjUserData = D("VideoUserList")->getSanJiuUserInfoByPhone($tmp['phone']);
                    if (!empty($sjUserData))  continue;
                    $importNums++;
                    $importData[] = $tmp;
                } 
                /************************* 导入逻辑结束    ***************************/
                // 删除上传的临时文件
                @unlink($_upload_file);

                // 导入类型：0-只导入  1-导入并注册
                if ($register == 1) {
                    if (count($importData) > 200) $this->error('批量注册三九用户数最好不要超过200个，若超过请分批导入！');

                    // 注册三九用户
                    foreach ($importData as $k => $v) {
                        // 还有重复的？暂时在这里加个判断吧
                        $sjUserData = D("VideoUserList")->getSanJiuUserInfoByPhone($v['phone']);
                        if (!empty($sjUserData))  continue;

                        $v['is_regist'] = 0;
                        $v['ctime'] = time();
                        $import_result = D('VideoUserList')->add($v);
                        if ($import_result === false) continue;  // 导入失败
                        $importSuccNums++;

                        $register_result = $this->registerSanJiuUser($v);
                        if ($register_result == 1) {  // 注册成功
                            $registerSuccNums++;
                            $update_sanjiu_result = D('VideoUserList')->updateSanJiuUserInfo($import_result, 1, '');
                        }else{  // 记录注册失败的原因
                            $failReason = array("2" => '手机号码不能为空', "3" => '手机号码填写错误', "4" => '该手机号码已经注册', "5" => '插表出错');
                            $update_sanjiu_result = D('VideoUserList')->updateSanJiuUserInfo($import_result, 0, $failReason[$register_result]);
                        }
                    }
                    $this->success('批量注册三九用户成功！导入数【'.$importNums.'】；导入成功数【'.$importSuccNums.'】；导入失败数【'.($importNums-$importSuccNums).'】；注册成功数【'.$registerSuccNums.'】；注册失败数【'.($importSuccNums-$registerSuccNums).'】。', UC('Admin/User/sanJiuUser'));
                }else{
                    if (count($importData) > 1000) $this->error('批量导入三九用户数最好不要超过1000个，若超过请分批导入！');
                    // 批量导入三九用户
                    foreach ($importData as $k => $v) {
                        // 还有重复的？暂时在这里加个判断吧
                        $sjUserData = D("VideoUserList")->getSanJiuUserInfoByPhone($v['phone']);
                        if (!empty($sjUserData))  continue;

                        $v['is_regist'] = 0;
                        $v['note'] = '未注册';
                        $v['ctime'] = time();
                        $import_result = D('VideoUserList')->add($v);
                        if ($import_result === false) continue;  // 导入失败
                        $importSuccNums++;
                    }
                    $this->success('批量注册三九用户成功！导入数【'.$importNums.'】；导入成功数【'.$importSuccNums.'】；导入失败数【'.($importNums-$importSuccNums).'】。', UC('Admin/User/sanJiuUser'));
                }
            }else{
                $this->error('Excel中的三九用户信息为空！');
            }
        }else{
            //模板下载地址
            $url = __BASE__.'data/_runtime/Data/sanjiuuserinfo.xlsx';
            $this->assign('url',$url);
            $this->assign('headline','添加三九用户');
            $this->display();
        }
    }

    /**
     * 注册三九用户
     * 1-注册成功
     * 失败原因：
     * 2-手机号码不能为空  3-手机号码填写错误  4-该手机号码已经注册  5-注册失败(插表出错)
     *
     */
    private function registerSanJiuUser($data){
        $phoneNum = $data['phone'];
        if (!$phoneNum) return 2;
        if (!is_numeric($phoneNum) || strlen($phoneNum) != 11) return 3;

        // 根据手机号码判断用户是否已经注册
        $userInfo = D('User')->getUserInfoByPhone($phoneNum);
        if ($userInfo && $userInfo['is_bindphone'] == 1) return 4;

        $password = $phoneNum;  // 初始密码是手机号码
        
        /**
         * 注册成功后需要插入的表：
         *     用户表、用户其他信息表、积分表
         *     用户认证信息表、用户账户信息
         */
        $userName = $data['uname']? $data['uname'] : $this->newGenerateUserName($phoneNum);  // 用户昵称
        $realName = $data['uname']? $data['uname'] : '';  // 用户真实姓名
        $loginSalt = rand(11111,99999);   // md5加密盐值

        $amount = 0;  // 初始化金额
        //$phoneData = D('Specialuser')->getSpecialUserInfoByPhone($phoneNum);
        //if (!empty($phoneData)) $amount = 10;  // 送10块金额

        $newUserId = $newUserOtherId = $newUserAuthId = $newUserAccountId = $newUserPointId = $updateLogResult = true;
        // 使用事务
        $userModel = D('User');
        $logModel = D('AccountslideLog');
        $userModel->startTrans();  // 开始事务
        $logModel->startTrans();

        // 添加新用户
        $newUserId = $userModel->addUser($phoneNum,$password,$loginSalt,1);

        // 添加用户其他信息
        $newUserOtherId = D('UserOtherinfo')->addUserOtherInfo($newUserId,$userName);

        // 添加用户认证信息
        $newUserAuthId = D('UserAuthentication')->addUserAuthInfo($newUserId,$realName);

        // 增加用户账户信息
        $newUserAccountId = D('UserAccount')->addUserAccountInfo($newUserId, $amount);

        // 增加用户积分信息
        $newUserPointId = D('UserPoint')->addUserPointInfo($newUserId);

        if ($amount > 0) {
            // 记录账户变动流水(无订单号则填-1)
            $updateLogResult = $logModel->addAccountslideLogInfo($newUserId, 1, 0, $amount, 15, -1, '注册赠送现金');
        }
        
        if ($newUserId !== false && $newUserOtherId !== false && $newUserAuthId !== false && $newUserAccountId !== false 
            && $newUserPointId !== false && $updateLogResult !== false) {
            $userModel->commit();    // 提交
            $logModel->commit();

            // 添加用户登录帐号
            $updateAccResult = D('User')->updateUserLoginAccount($newUserId);

            // 用户注册可获得积分(增加用户积分)
            /*
            $changePoint = 5;   // 变动积分额(增加)
            $updateResult = $this->changeUserPoint($newUserId, $changePoint, 1, 0);
            */

            // 推送消息
            if ($amount > 0) {
                $title = '欢迎注册使用药师帮！';
                $content = '亲爱的用户：欢迎注册使用药师帮！'. $amount .'元现金已经送达您的账户，点击“药财富”可查看。药师帮，帮药师，药师帮致力于用移动互联网为药店和药师服务：提供移动医药培训课堂，帮助广大药师通过执业药师资格考试；构建移动医药商务平台，帮助广大药师实现专业提升、薪酬提升和社会地位提升。如有疑问，欢迎来电咨询4009222115。';
                $this->push($newUserId, $title, $content);
            }
            return 1;
        }else{
            $userModel->rollback();  // 回滚
            $logModel->rollback();
            return 5;
        }
    }

    /**
     * 黑名单用户
     */
    public function blackUserListAct(){
        $this->_set_page();
        $title = I('post.title');
        $list = D('BlackuserList')->getBlackUserData($this->_page, $this->_pagesize, $title);

        $count = $list['count'];
        $param = array(
                'result'=>$list['data'],             //分页用的数组
                'count'=>$count,             //数组的量
                'listvar'=>'list',           //分页循环变量
                'template'=>'Libfetch:blackUserListlib' //ajax更新模板
        );
        $this->_cjax_page($param);
        //表单顶部html以及底部html
        $top_html = cjax_table_top(array('field_name'=>'title', 'field_title'=>'昵称/姓名/手机号'));
        $bottom_html = cjax_table_bottom(array('count'=>$count, 'field_name'=>'title'));
        $this->assign('top_html', $top_html);
        $this->assign('bottom_html', $bottom_html);
        $this->display();
    }

    /**
     * 导入黑名单用户
     */
    public function addBlackUserAct(){
        if(IS_POST){
            if (empty($_POST['state'])) $this->error("请选择禁止状态！");

            $data = $_POST;
            $phonelist = $data['phonelist'];  // 用户手机号码
            $state = $data['state'];  // 账号状态：1-禁止登陆  2-禁止扫码  3-禁止提现
            
            if (!empty($phonelist) || (!empty($_FILES['xls_file']) && $_FILES['xls_file']['size'] > 0)) {
                $newBlackUID = array();  // 新的黑名单用户ID
                $oldBlackUID = array();  // 重复的黑名单用户：需要更新状态
                if (!empty($phonelist)) {
                    $tmp_phoneNums = explode(';', $phonelist);
                    foreach ($tmp_phoneNums as $k => $v) {
                        if (empty($v)) continue;
                        $tmp_blackdata = D('BlackuserList')->getBlackUserInfoByPhone($v);
                        if ($tmp_blackdata['code'] == 3) continue;  // 该手机号码没有注册

                        $blackUserData = $tmp_blackdata['data'];
                        if ($tmp_blackdata['code'] == 2) {  // 该手机号码不在黑名单内：需要添加
                            if (!in_array($blackUserData['id'], $newBlackUID)) $newBlackUID[] = $blackUserData['id'];
                        }else{  // 该手机号码已经在黑名单中：需要更新状态
                            if (!in_array($blackUserData['user_id'], $oldBlackUID)) $oldBlackUID[] = $blackUserData['user_id'];
                        }
                    }
                }else{  // 从Excel文件导入
                    $_upload_file       = $_FILES['xls_file']['tmp_name'];
                    $_upload_file_name  = $_FILES['xls_file']['name'];
                    $_file_extension    = strtolower(pathinfo( $_upload_file_name,PATHINFO_EXTENSION));// 得知这个附件的文件类型
                        
                    if ($_file_extension != "xlsx") $this->error("请上传扩展名为 xlsx 的EXCEL2007文件。");
                    
                    Vendor('PHPExcel.IOFactory');
                    Vendor('PHPExcel.PHPExcel');        
                    $PHPExcel_IOFactory = new \PHPExcel_IOFactory();
                        
                    $inputFileType = 'Excel2007';
                    $objReader =  $PHPExcel_IOFactory->createReader($inputFileType);
            
                    $objPHPExcel = $objReader->load($_upload_file);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objSheet = $objPHPExcel->getActiveSheet();
                    $_exam_title = $objSheet->getTitle();       
                    
                    /************************* 导入逻辑开始     ***************************/
                    $highestRow = $objSheet->getHighestRow();           //取得总行数
                    $i = 1;  // 从第二行开始
                    while($i <= $highestRow)
                    {
                        $i++;  // 继续下一行取值
                        //取出excel整行数据
                        $tmp_phone = $objSheet->getCell('A'.$i)->getValue();
                        if (empty($tmp_phone)) continue;
                        $tmp_blackdata = D('BlackuserList')->getBlackUserInfoByPhone($tmp_phone);
                        if ($tmp_blackdata['code'] == 3) continue;  // 该手机号码没有注册

                        $blackUserData = $tmp_blackdata['data'];
                        if ($tmp_blackdata['code'] == 2) {  // 该手机号码不在黑名单内：需要添加
                            if (!in_array($blackUserData['id'], $newBlackUID)) $newBlackUID[] = $blackUserData['id'];
                        }else{  // 该手机号码已经在黑名单中：需要更新状态
                            if (!in_array($blackUserData['user_id'], $oldBlackUID)) $oldBlackUID[] = $blackUserData['user_id'];
                        }
                    } 
                    /************************* 导入逻辑结束    ***************************/
                    // 删除上传的临时文件
                    @unlink($_upload_file);
                }

                // 限制每次导入操作不要超过50000个用户
                $num = count($newBlackUID);
                if ($num > 10000) $this->error('手机号码数量最好不要超过10000个，若超过请分批添加！');

                $login_priv = $scan_priv = $cash_priv = 0;
                if (!empty($state)) {
                    if (in_array(1, $state)) $login_priv = 1;  // 禁止登陆
                    if (in_array(2, $state)) $scan_priv = 1;   // 禁止扫码
                    if (in_array(3, $state)) $cash_priv = 1;   // 禁止提现
                }

                // 导入表中
                $successNum = 0;  // 成功导入的黑名单数量
                foreach ($newBlackUID as $k => $v) {
                    $result = D('BlackuserList')->addBlackUserInfo($v, $login_priv, $scan_priv, $cash_priv);
                    if ($result !== false) $successNum++;
                }

                // 更改已经存在的黑名单记录状态
                if (!empty($oldBlackUID)) {
                    $result = D('BlackuserList')->updateBlackUserState($oldBlackUID, $login_priv, $scan_priv, $cash_priv);
                    if ($result !== false) $successNum += $result;
                }

                // 删除用户userToken，踢用户下线
                if ($login_priv == 1) {  // 禁止登陆
                    foreach ($newBlackUID as $k => $v) {
                        $this->kickUserOffline($v);  // 删除用户userToken，踢用户下线
                    }
                    
                    foreach ($oldBlackUID as $k => $v) {
                        $this->kickUserOffline($v);  // 删除用户userToken，踢用户下线
                    }
                }
                
                $this->success('导入黑名单用户成功，本次增加黑名单用户数量：' . $successNum, UC('Admin/User/blackUserList'));
            }else{
                $this->error('用户手机号不能为空！');
            }
        }else{
            $opStateDesc = D("BlackuserList")->newGetOpStateDesc();
            $this->assign("opStateDesc", $opStateDesc);

            //模板下载地址
            $url = __BASE__.'data/_runtime/Data/phoneinfo.xlsx';
            $this->assign('url',$url);
            $this->assign('headline','导入黑名单用户');
            $this->display();
        }
    }

    /**
     * 编辑黑名单用户的信息
     */
    public function editBlackUserAct(){
        if (IS_POST) {
            $id = I("post.id");
            $state = I("post.state");
            $note = I("post.note");

            if (empty($id)) $this->error("非法操作！");
            if (empty($note)) $this->error("拉黑原因不能为空！");

            $blackUserData = D("BlackuserList")->getBlackUserInfoByID($id);
            if (!$blackUserData) $this->error("该黑名单记录不存在！");

            $login_priv = $scan_priv = $cash_priv = 0;
            if (!empty($state)) {
                if (in_array(1, $state)) $login_priv = 1;  // 禁止登陆
                if (in_array(2, $state)) $scan_priv = 1;   // 禁止扫码
                if (in_array(3, $state)) $cash_priv = 1;   // 禁止提现
            }

            $data['id'] = $id;
            $data['login_priv'] = $login_priv;
            $data['scan_priv'] = $scan_priv;
            $data['cash_priv'] = $cash_priv;
            $data['note'] = $note;
            $data['mtime'] = time();
            if (D("BlackuserList")->save($data)!== false) {
                if ($login_priv == 1) {  // 禁止登陆
                    $userID = $blackUserData['user_id'];
                    $this->kickUserOffline($userID);  // 删除用户userToken，踢用户下线
                }
                
                $this->success("修改成功！", UC("User/blackUserList"));
            } else {
                $this->error("修改失败！".D("BlackuserList")->getlastsql().print_r($data, true));
            }
        }else{
            $id = intval(I("get.id"));
            if (!$id) $this->error("非法操作！");

            $data = D("BlackuserList")->getBlackUserInfoByID($id);
            if (!$data) $this->error("该黑名单记录不存在！");

            $userData = D('User')->getUserById($data['user_id']);
            if (!$userData) $this->error("该用户不存在！");

            $userOtherData = D('UserOtherinfo')->getByUserId($data['user_id']);
            $userAuthData = D('UserAuthentication')->get_one_data($data['user_id']);
            $data['phone'] = $userData['phone'];
            $data['uname'] = $userOtherData['uname'];
            $data['realname'] = $userAuthData['real_name'];

            $accountState = array();
            if ($data['login_priv'] == 1) $accountState[] = 1;  // 禁止登陆
            if ($data['scan_priv'] == 1) $accountState[] = 2;   // 禁止扫码
            if ($data['cash_priv'] == 1) $accountState[] = 3;   // 禁止提现
            $data['accountState'] = $accountState;

            $opStateDesc = D("BlackuserList")->newGetOpStateDesc();
            $this->assign("opStateDesc", $opStateDesc);

            $this->assign("headline", "编辑黑名单用户信息");
            $this->assign("action_name", ACTION_NAME);
            $this->assign("info",  $data);
            $this->display();
        }
    }

    /**
     * 删除某个黑名单用户
     */
    public function delBlackUserAct() {
        $id = intval(I("post.id"));
        if (empty($id)) $this->error("非法操作！");

        $status = D("BlackuserList")->delete($id);
        if ($status !== false) {
            $this->success("删除成功！", UC("User/blackUserList"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 将某个用户加入黑名单
     */
    public function addUserIntoBlackListAct() {
        $userID = intval(I("post.id"));
        $note = I("post.note");
        $state = I("post.state");  // 账号状态：1-禁止登陆  2-禁止扫码  3-禁止提现
        if (empty($userID)) $this->error("非法操作！");
        if (empty($note)) $this->error("拉黑原因不能为空！");
        if (empty($state)) $this->error("请选择禁止状态！");

        $userData = D('User')-> getUserById($userID);
        if (empty($userData)) $this->error("该用户不存在！");

        $login_priv = $scan_priv = $cash_priv = 0;
        if (in_array(1, $state)) $login_priv = 1;  // 禁止登陆
        if (in_array(2, $state)) $scan_priv = 1;  // 禁止扫码
        if (in_array(3, $state)) $cash_priv = 1;  // 禁止提现

        // 将用户加入黑名单
        $result = false;
        $isExist = D('BlackuserList')->getBlackUserInfoByUserID($userID);
        if ($isExist) {  // 存在则更改状态
            $result = D('BlackuserList')->updateBlackUserState($userID, $login_priv, $scan_priv, $cash_priv, $note);
        }else{
            $result = D('BlackuserList')->addBlackUserInfo($userID, $login_priv, $scan_priv, $cash_priv, $note);  // 账号状态：1-禁止登陆 2-禁止扫码 3-禁止提现
        }
        if ($result !== false) {
            // 删除用户userToken，踢用户下线
            if ($login_priv == 1) $this->kickUserOffline($userID);  // 禁止登陆
            $this->success("加入黑名单成功！", UC("User/user"));
        } else {
            $this->error("加入黑名单失败！");
        }
    }

    // 踢用户下线
    private function kickUserOffline($userID){
        // 删除用户userToken，踢用户下线
        $userToken = p\MyRedis::hashGet("user.info:$userID",'usertoken');
        if ($userToken) {
            p\MyRedis::delete("token.to.userid:$userToken");  // 删除用户userToken
            p\MyRedis::delete("user.info:$userID");
        }
    }
    
    /**
     * 转移身份（从总部转到分布）
     */
    public function transferIdentityAct(){
    	
    	$user_id = I('post.user_id');
    	$saveData['drugstore_type'] = 1;
    	$res = D('UserAuthentication')->where(array("user_id"=>$user_id))->save($saveData);
    	$addressRes = D('UserAddress')->where(array("user_id"=>$user_id,"area_id"=>0))->delete();
    	if($res){
    		$this->success("转走成功！");
    	}else{
    		$this->success("转走失败！");
    	}
    	
    }
    
    /**
     * 给用户更换药店
     */
    public function change_storeAct(){
    	if (IS_POST) {
            $storeID = I('post.store_id');  // 更换药店ID
            $userID = I('post.user_id');    // 用户ID

            if (empty($storeID) || empty($userID)) $this->error('非法操作！');
            $storeData = D("UserDrugstorebranch")->getUserDrugStoreDataByCondition($userID, 1, $storeID);

            if (empty($storeData)) $this->error('您还未加入药店、或选择药店错误！');

            $storeModel = D("UserDrugstorebranch");
            $storeModel->startTrans();  // 开始事务

            $ctime = time();
            $update_result = $add_result = true;
            $whereSql['user_id'] = $userID;
            $whereSql['status'] = 1;

            $updateData['status'] = 0;  // 离职
            $updateData['leave_time'] = $updateData['mtime'] = $ctime;
            $update_result = $storeModel->where($whereSql)->save($updateData);
            $add_result = $storeModel->joinStoreByUid($userID, $storeID, $ctime);
    		if($update_result !== false && $add_result !== false){
    			$storeModel->commit();    // 提交
                $this->success('更换药店成功！');
    		}else{
                $storeModel->rollback();  // 回滚
                $this->error('更换药店失败！');
            }
    	}else{
    		//所有省份
    		$province = D('Areas')->getProvince();
    		$this->assign("province",$province);
    		$this->assign('user_id', I('get.user_id'));
    		$this->display();
    	}
    }

    public function new_change_storeAct(){
        if (IS_POST) {
            $storeID = I('post.store_id');  // 更换药店ID
            $userID = I('post.user_id');    // 用户ID
            $position = I('post.position');    // 职务：0-店员  1-店长

            if (empty($storeID) || empty($userID)) $this->error('非法操作！');
            if (!is_numeric($storeID)) $this->error('药店ID必须为整数！');

            $storeData = D("DrugstoreBranch")->getDrugstoreById($storeID);
            if (empty($storeData)) $this->error('药店ID填写错误，不存在此药店！');

            $storeData = D("UserDrugstorebranch")->getUserDrugStoreDataByCondition($userID, 1);
            if (empty($storeData)) $this->error('您还未加入药店！');
            if ($storeData['drugstore_branch_id'] == $storeID && $storeData['position'] == $position) $this->error('更换的药店为已加入的药店，且职务不变！');

            if ($position == 1) {  // 如果选择职务为店长，需要判断所加入的药店是否已有店长
                $hasManager = D("UserDrugstorebranch")->hasStoreManagerByStoreId($storeID);
                if ($hasManager) $this->error('您加入的药店已有店长！');
            }

            $storeModel = D("UserDrugstorebranch");
            $storeModel->startTrans();  // 开始事务

            $ctime = time();
            $update_result = $add_result = true;
            $whereSql['user_id'] = $userID;
            $whereSql['status'] = 1;

            $updateData['status'] = 0;  // 离职
            $updateData['leave_time'] = $updateData['mtime'] = $ctime;
            $update_result = $storeModel->where($whereSql)->save($updateData);
            $add_result = $storeModel->joinStoreByUid($userID, $storeID, $ctime, $position);
            if($update_result !== false && $add_result !== false){
                $storeModel->commit();    // 提交
                $this->success('更换药店成功！');
            }else{
                $storeModel->rollback();  // 回滚
                $this->error('更换药店失败！');
            }
        }else{
            $userID = I('get.user_id');
            $noNeedStoreId = I('get.noNeedStoreId');
            if (empty($userID)) $this->error('非法操作！');
            
            $storeData = D("UserDrugstorebranch")->getUserDrugStoreDataByCondition($userID);
            if (empty($storeData)) $this->error('您还未加入药店！');

            $old_store_id = $storeData['drugstore_branch_id'];
            $joinStoreData = D("DrugstoreBranch")->getDrugstoreById($old_store_id);

            // 获取店长信息
            $managerInfo = D("UserDrugstorebranch")->getStoreManagerInfoByStoreId($old_store_id);

            $data = array();
            $data['storeid'] = $old_store_id;
            $data['storetitle'] = $joinStoreData['storetitle'];
            $data['userid'] = $userID;
            $data['position'] = $storeData['position'];
            $data['manager'] = $managerInfo;
            $this->assign('info', $data);
            $this->assign('noNeedStoreId', $noNeedStoreId);
            $this->display();
        }
    }

    /**
     * 更换手机号码
     */
    public function changeLoginPhoneAct(){
        if (IS_POST) {
            $phoneNum = I('post.phonenum');  // 更换的手机号码
            $userID = I('post.user_id');     // 用户ID

            if (empty($userID)) $this->error('非法操作！');
            if (empty($phoneNum) || !is_numeric($phoneNum) || strlen($phoneNum) != 11) $this->error('请输入正确的手机号码！');

            // 判断手机号码是否已经注册
            $userData = D('User')->getUserInfoByPhone($phoneNum);
            if (!empty($userData)) $this->error('该手机号码已经注册！');

            $whereSql['id'] = $userID;
            $updateData['phone'] = $phoneNum;
            $updateData['is_bindphone'] = 1;
            $updateData['mtime'] = time();
            $update_result = D('User')->where($whereSql)->save($updateData);
            if($update_result !== false){
                $this->success('更换手机号码成功！');
            }else{
                $this->error('更换手机号码失败！');
            }
        }else{
            $userID = I('get.user_id');
            if (empty($userID)) $this->error('非法操作！');
            $this->assign('user_id', $userID);
            $this->display();
        }
    }

    /**
     * 变更药店白名单用户
     */
    public function changeStoreWhiteListAct(){
        $this->_set_page();
        $title = I('post.title');
        $list = D('ChangestoreWhitelist')->getAllChangeStoreWhiteListData($this->_page, $this->_pagesize, $title);

        $count = $list['count'];
        $param = array(
                'result'=>$list['data'],             //分页用的数组
                'count'=>$count,             //数组的量
                'listvar'=>'list',           //分页循环变量
                'template'=>'Libfetch:changestoreWhitelistlib' //ajax更新模板
        );
        $this->_cjax_page($param);
        //表单顶部html以及底部html
        $top_html = cjax_table_top(array('field_name'=>'title', 'field_title'=>'昵称/姓名/手机号'));
        $bottom_html = cjax_table_bottom(array('count'=>$count, 'field_name'=>'title'));
        $this->assign('top_html', $top_html);
        $this->assign('bottom_html', $bottom_html);
        $this->display();
    }

    /**
     * 导入变更药店白名单用户
     */
    public function addChangeStoreWhiteListAct(){
        if(IS_POST){
            $data = $_POST;
            $phonelist = $data['phonelist'];  // 用户手机号码
            $reason = $data['reason'];  // 加入白名单原因

            $opUid = $this->_account['uid'];       // 操作员ID
            $opName = $this->_account['account'];  // 操作员账号
            
            if (!empty($phonelist) || (!empty($_FILES['xls_file']) && $_FILES['xls_file']['size'] > 0)) {
                $whiteListData = array();  // 新的白名单用户
                if (!empty($phonelist)) {
                    $tmp_phoneNums = explode(';', $phonelist);
                    foreach ($tmp_phoneNums as $k => $v) {
                        if (empty($v)) continue;

                        // 根据手机号码获取用户ID
                        $userData = D("User")->getUserInfoByPhone($v);
                        if (empty($userData)) continue;

                        $uid = $userData['id'];
                        $tmp_data = D('ChangestoreWhitelist')->getChangeStoreWhiteListDataByUserID($uid);
                        if (!empty($tmp_data)) continue;  // 已经存在白名单中

                        $tmpData = array();
                        $tmpData['user_id'] = $uid;
                        $tmpData['reason'] = $reason;
                        $whiteListData[] = $tmpData;
                        unset($tmpData);
                    }
                }else{  // 从Excel文件导入
                    $_upload_file       = $_FILES['xls_file']['tmp_name'];
                    $_upload_file_name  = $_FILES['xls_file']['name'];
                    $_file_extension    = strtolower(pathinfo( $_upload_file_name,PATHINFO_EXTENSION));// 得知这个附件的文件类型
                        
                    if ($_file_extension != "xlsx") $this->error("请上传扩展名为 xlsx 的EXCEL2007文件。");
                    
                    Vendor('PHPExcel.IOFactory');
                    Vendor('PHPExcel.PHPExcel');        
                    $PHPExcel_IOFactory = new \PHPExcel_IOFactory();
                        
                    $inputFileType = 'Excel2007';
                    $objReader =  $PHPExcel_IOFactory->createReader($inputFileType);
            
                    $objPHPExcel = $objReader->load($_upload_file);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objSheet = $objPHPExcel->getActiveSheet();
                    $_exam_title = $objSheet->getTitle();       
                    
                    /************************* 导入逻辑开始     ***************************/
                    $highestRow = $objSheet->getHighestRow();           //取得总行数
                    $i = 1;  // 从第二行开始
                    while($i <= $highestRow)
                    {
                        $i++;  // 继续下一行取值
                        //取出excel整行数据
                        $tmp_phone = $objSheet->getCell('A'.$i)->getValue();
                        $tmp_reason = $objSheet->getCell('B'.$i)->getValue();
                        if (empty($tmp_phone)) continue;

                        // 根据手机号码获取用户ID
                        $userData = D("User")->getUserInfoByPhone($tmp_phone);
                        if (empty($userData)) continue;

                        $uid = $userData['id'];
                        $tmp_data = D('ChangestoreWhitelist')->getChangeStoreWhiteListDataByUserID($uid);
                        if (!empty($tmp_data)) continue;  // 已经存在白名单中
                        $tmp_reason = $tmp_reason? $tmp_reason : $reason;

                        $tmpData = array();
                        $tmpData['user_id'] = $uid;
                        $tmpData['reason'] = $tmp_reason;
                        $whiteListData[] = $tmpData;
                        unset($tmpData);
                    } 
                    /************************* 导入逻辑结束    ***************************/
                    // 删除上传的临时文件
                    @unlink($_upload_file);
                }

                // 限制每次导入操作不要超过50000个用户
                $num = count($whiteListData);
                if ($num <= 0) $this->error('填写手机号码不正确，或该用户已加入白名单！');
                if ($num > 50000) $this->error('手机号码数量最好不要超过50000个，若超过请分批推送！');

                // 导入表中
                $successNum = 0;  // 成功导入的白名单数量
                foreach ($whiteListData as $k => $v) {
                    $add_result = D('ChangestoreWhitelist')->addChangeStoreWhiteListData($v['user_id'], $v['reason'], $opName, $opUid);
                    if ($add_result !== false) $successNum++;
                }
                
                $this->success('导入变更药店白名单用户成功，本次增加白名单用户数量：' . $successNum, UC('Admin/User/changeStoreWhiteList'));
            }else{
                $this->error('用户手机号不能为空！');
            }
        }else{
            //模板下载地址
            $url = __BASE__.'data/_runtime/Data/phoneinfo.xlsx';
            $this->assign('url',$url);
            $this->assign('headline','导入变更药店白名单用户');
            $this->display();
        }
    }

    /**
     * 删除某个白名单用户
     */
    public function delChangeStoreWhiteListAct() {
        $id = I("post.id");
        if (empty($id)) $this->error("非法操作！");

        $del_result = D("ChangestoreWhitelist")->delete($id);
        if ($del_result !== false) {
            $this->success("删除成功！", UC("User/blackUserList"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 录入白名单原因
     */
    public function editChangeStoreWhiteListReasonAct() {
        $logid = I("post.id");
        $reason = I("post.reason");
        if (empty($logid)) $this->error("非法操作！");
        if (empty($reason)) $this->error("原因不能为空！");

        $data = D('ChangestoreWhitelist')->getChangeStoreWhiteListDataByID($logid);
        if (empty($data)) $this->error("该记录已不存在！");

        $opUid = $this->_account['uid'];       // 操作员ID
        $opName = $this->_account['account'];  // 操作员账号

        // 录入白名单原因
        $update_result = D("ChangestoreWhitelist")->updateReason($logid, $reason, $opName, $opUid);
        if ($update_result !== false) {
            $this->success("录入原因成功！", UC("User/changeStoreWhiteList"));
        } else {
            $this->error("录入原因失败！");
        }
    }
}
