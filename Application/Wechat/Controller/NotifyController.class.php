<?php

/**
 * 通知模块
 * 
 */
namespace Wechat\Controller;
use Think\Controller;

class NotifyController extends Controller {

    //通知过期的票
    public function pushNotifyAct(){
        $pushlist = D('TicketOrder')->ifPushTicketMsg();
        foreach($pushlist as $push){
            $issend = S('push_'.$push['open_id'].'_'.$push['sn']);
            if($issend){
                continue;
            }
            \Wechat\Logic\PushLogic::pushTextMsg("魔乐城票务提醒：\n  尊敬的用户：您购买的魔乐城体验票还有2天就要过期了，请尽快入场消费。感谢您的支持，祝您玩的愉快！");
            S('push_'.$push['open_id'].'_'.$push['sn'],true);
        }
        echo 1;
    }
}