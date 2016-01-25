<?php

/**
 * 售票模块
 * 
 */
namespace Wechat\Controller;
use Think\Controller;

class CommonController extends Controller {

    public function _initialize() {

        if(SERVER_ENVIRONMENT == 'LOCAL_HOST'){
            session('openid','oZcK7wtW4NB-hr5I1_XKKfoC6zV8');//测试openid
        }
        if( ACTION_NAME != 'notifyurl'){//支付通知--过滤掉
            if(!session('openid')){
                $this->_session_openid();
            }
        }
        
    }
    
    protected function _session_openid(){
        
        $wechatObj = wechatInstance();
        if($_GET['code']){
            $result = $wechatObj->getOauthAccessToken();
            $userInfo = $wechatObj->getUserInfo($result['openid']);
            $this->_saveUserInfo($userInfo);
            session('openid',$result['openid']);
        }else{
            $callBackUrl = __BASE__.substr(UC(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME),1);
            $url = $wechatObj->getOauthRedirect($callBackUrl, '', 'snsapi_base');
            redirect($url);
        }
        
    }
    
    /**
     * openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]
     * 
     */
    protected function _saveUserInfo($userInfo){
        
        $check = D('User')->where(array('open_id'=>$userInfo['openid']))->find();
        if(!$check && $userInfo['openid']){
            $array['open_id']    = $userInfo['openid'];
            $array['headimgurl'] = $userInfo['headimgurl'];
            $array['nickname']   = $userInfo['nickname'];
            $array['sex']        = $userInfo['sex'];
            $array['ctime']      = time();
            $array['country']    = $userInfo['country'];
            $array['province']   = $userInfo['province'];
            $array['city']       = $userInfo['city'];
            
            D('User')->add($array);
        }
        
    }
    
    /**
     * 推送消息
     * @param string $openid 用户openid
     */
    protected function checkIfPushTicketMsg($openid){
        
        return D('TicketOrder')->ifPushTicketMsg($openid);
        
    }

   

}
