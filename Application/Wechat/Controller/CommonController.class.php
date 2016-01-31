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

        $weobj = wechatInstance();
        $signature = $weobj->getJsSign(currentUrl(),time(),md5(rand(1,9999)),C('WECHAT_APPID'));
        $signature['jsApiList'] = ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'];
        $this->assign('signature',str_replace("\\/", "/", json_encode($signature)));

        $sharetitle = getSysConfig('share-title');
        $sharedesc = getSysConfig('share-desc');
        $shareimgUrl = getSysConfig('share-imgUrl');
        $sharelink = getSysConfig('share-link');

        $this->assign('sharetitle',$sharetitle);
        $this->assign('sharedesc',$sharedesc);
        $this->assign('shareimgUrl',$shareimgUrl);
        $this->assign('inviteurl',domainurl().UC($sharelink,array('invate'=>getOpenid())));
        
    }
    
    protected function _session_openid(){
        
        $wechatObj = wechatInstance();
        if($_GET['code']){
            $result = $wechatObj->getOauthAccessToken();
            $userInfo = $wechatObj->getUserInfo($result['openid']);
            $this->_saveUserInfo($userInfo);
            session('openid',$result['openid']);
        }else{
            //$callBackUrl = __BASE__.substr(UC(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME),1);
            $callBackUrl = currentUrl();
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
        $array['open_id']    = $userInfo['openid'];
        $array['headimgurl'] = $userInfo['headimgurl'];
        $array['nickname']   = $userInfo['nickname'];
        $array['sex']        = $userInfo['sex'];
        $array['ctime']      = time();
        $array['country']    = $userInfo['country'];
        $array['province']   = $userInfo['province'];
        $array['city']       = $userInfo['city'];
        
        if(!$check && $userInfo['openid']) D('User')->add($array);
        
        if($check &&(!$check['nickname'] || !$check['headimgurl'])){
            unset($array['open_id']);
            D('User')->where(array('open_id'=>$userInfo['openid']))->save($array);
        }
        
    }
    

   

}
