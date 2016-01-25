<?php
namespace Wechat\Logic; 

class PushLogic{

    public static function pushTextMsg($openid,$msg,$wechat=''){
        if(!$wechat){
            $wechat = wechatInstance();
        }
        $data['touser'] = $openid;
        $data['msgtype'] = 'text';
        $data['text'] = array('content'=>$msg);
        return $wechat->sendCustomMessage($data);
    }

}