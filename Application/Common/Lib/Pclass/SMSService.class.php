<?php

namespace Common\Lib\Pclass;
defined( 'IN_HEAVEN' ) or die ( 'Hacking Attempt!' );
/**---
 *       
 ******************
 * 基础服务--短信服务
 * @author 黄业生 ysbang
 * eg:
    $code = SMSService::sendSMS('18938657523','短信内容'); //单发
    $code = SMSService::sendSMS('18938657523,18926490312','短线内容');//群发
    if($code == 100){
        echo "发送成功";
    }
    else{
        echo "发送失败";
    }
 ******************
 **/
class SMSService {

    const USERNAME = '70201084';     //用户账号
    const PASSWORD = '18938657523';     //密码
    const APPKEY_HAOSERVICE = '2f384d7b8a6b496aa04815c3ff2ac863';     //HaoService App Key
    const ACCOUNT_CHANZOR = 'zcs8092';     //畅卓短信接口账号
    const PASSWORD_CHANZOR = '133158';     //畅卓短信接口密码

    // 短信模板(互忆无线)
    const TEMPLATE_SMS = array(
        "28367" => "您已成功购买【变量】等【变量】个药品，我们将尽快为您安排送货，欢迎再次采购。",
        "27955" => "顾客【变量】的订单已经确认收货，您获得【变量】元报酬，已经进入您的药师帮余额账户咯！",
        "27954" => "您采购的【变量】采购差价【变量】元已返还至您的药师帮账户，快快进入【药师帮】查收哦~ 下期继续抢购，价格更加优惠！",
        "27952" => "恭喜您！您申请的提现金额已转出到您的支付宝账号，请注意查收哦！",
        "27951" => "已向您的支付宝账号【变量】转账【变量】元，预计两个小时内到账，请注意查收哦！",
        "27950" => "很遗憾，您的提现申请未能通过，请您仔细阅读提现说明并认真核对相关资料。如有问题，欢迎来电咨询020-89772072。",
        "27805" => "您的验证码是：【变量】，此验证码10分钟内有效。",
        "29013" => "您的药师帮账号为【变量】，密码为【变量】；【变量】元现金券已经发放到您的药师帮账户，请下载app登录使用。",
        "29244" => "感谢您的参与！【变量】元现金券已经发放到您的药师帮账户，请登录app使用。",
        "29536" => "您已成功下单【变量】（【变量】）等【变量】种药品，请及时付款。我们将尽快为您安排送货，欢迎再次采购。"
    );

    /**-------
     * 发送短信
     * @param String $phone 手机号码（如多个，用英文逗号分割）
     * @param String $content 短信内容
     * @param String $time 定时发送时间
     * @return int 状态码
        状态码:
        100 发送成功
        101 验证失败
        102 短信不足
        103 操作失败
        104 非法字符
        105 内容过多
        106 号码过多
        107 频率过快
        108 号码内容空
        109 账号冻结
        110 禁止频繁单条发送
        111 系统暂定发送
        112 号码不正确
        120 系统升级
    ------*/
    public static function sendSMS($phone,$content,$time='')
    {
        $content = iconv("UTF-8","gb2312",$content);
        $http = 'http://api.duanxin.cm/';
        $data = array
            (
            'action'=>'send',
            'username'=>self::USERNAME,                  //用户账号
            'password'=>strtolower(md5(self::PASSWORD)), //MD5位32密码
            'phone'=>$phone,                    //号码
            'content'=>$content,                    //内容
            'time'=>$time                       //定时发送
            );
        $re= self::postSMS($http,$data);                  //POST方式提交
        return trim($re);
    }


    private static function postSMS($url,$data='')
    {
        $row = parse_url($url);
        $host = $row['host'];
        $port = $row['port'] ? $row['port']:80;
        $file = $row['path'];
        while (list($k,$v) = each($data)) 
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.0\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;      
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }

    /**
     * HaoService 短信发送接口
     * @param $phone          手机号码
     * @param $identifyCode   验证码
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSMSByHaoService($phone,$identifyCode){
        // GET方式请求
        $tplValue = urlencode('#code#=' . $identifyCode . '&#company#=药师帮');
        $url = 'http://apis.haoservice.com/sms/send?key=' . self::APPKEY_HAOSERVICE . '&mobile=' . $phone . '&tpl_id=1&tpl_value=' . $tplValue;
        
        $curl = new Curl();
        $tmpInfo = $curl->get($url);
        $returnData = json_decode($tmpInfo,true);  // 转为数组
        if (!empty($returnData) && $returnData['error_code'] == 0) return true;
        else return false;
    }

    /**
     * 畅卓短信发送接口
     * @param String $phone 手机号码（如多个，用英文逗号分割）
     * @param String $content 短信内容
     * @param String $time 定时发送时间：为空表示立即发送，定时发送格式2010-10-24 09:08:10
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSMSByChanzor($phone,$content,$time=''){
        $target = "http://sms.chanzor.com:8001/sms.aspx";
        $post_data = "action=send&userid=&account=" . self::ACCOUNT_CHANZOR . "&password=" . self::PASSWORD_CHANZOR . "&mobile=" . $phone . "&sendTime=" . $time . "&content=".rawurlencode($content);
        //$binarydata = pack("A", $post_data);
        $gets = self::postSMSByChanzor($post_data, $target);
        $start=strpos($gets,"<?xml");
        $data=substr($gets,$start);
        $xml=simplexml_load_string($data);
        $returnData = json_decode(json_encode($xml),true);  // 转为数组
        if (!empty($returnData) && strcasecmp($returnData['returnstatus'],'success') == 0) return true;
        else return false;
    }

    private static function postSMSByChanzor($data, $target){
        $url_info = parse_url($target);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        $httpheader .= $data;
        $fd = fsockopen($url_info['host'], 80);
        fwrite($fd, $httpheader);
        $gets = "";
        while(!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);
        return $gets;
    }

    /**
     * 短信发送总接口(version 2.0)
     * @param $phone          手机号码
     * @param $identifyCode   验证码
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function newSendSMS($phone,$identifyCode){
        // 电信短信发送接口
        $result = YsSmSService::sendSms($phone,$identifyCode);
        self::recordSmsLog(1,$result);  // 记录电信发送情况

        // 互亿无线短信发送接口
        if (!$result) {
            $result = HuyiSmsService::sendSms($phone,$identifyCode);
            self::recordSmsLog(2,$result);  // 记录互亿无线发送情况
        }

        // 莫名短信发送接口
        if (!$result) {
            $status = SMSService::sendSMS($phone,'您的验证码为：' . $identifyCode);
            if ($status == 100) $result = true;
            self::recordSmsLog(3,$result);  // 记录莫名发送情况
        }

        // HaoService短信发送接口
        //if (!$result) $result = SMSService::sendSMSByHaoService($phone,$identifyCode);
        return $result;
    }

    /**
     * 记录各短信渠道发送的信息
     * Redis中的键名：
     * ysb.smsinfo:$type  哈希类型   failnum->发送失败的次数  lastfailtime->上一次发送失败的时间戳
     *                                sucessnum->发送成功的次数  lastsucesstime->上一次发送成功的时间戳
     * @param $type  1-电信短信接口  2-互亿无线  3-莫名
     * @param $flag  true-发送成功  false-发送失败
     * @param $isCode  true-验证码短信  false-非验证码短信
     */
    private static function recordSmsLog($type, $flag, $isCode = true){
        $hashKey = '';
        if ($isCode) $hashKey = "ysb.smsinfo:$type";
        else $hashKey = "ysb.smsinfo.cnt:$type";

        $isFound = MyRedis::exists($hashKey);
        if (!$isFound) {  // 没找到
            $data['failnum'] = 0;
            $data['lastfailtime'] = 0;
            $data['sucessnum'] = 0;
            $data['lastsucesstime'] = 0;
            MyRedis::hashSet($hashKey, $data);
        }

        $data = array();
        if ($flag) {  // 发送成功
            $data['lastsucesstime'] = time();
            MyRedis::hashSet($hashKey, $data);
            MyRedis::hashInc($hashKey,'sucessnum',1);   // 发送成功次数加1
        }else{
            $data['lastfailtime'] = time();
            MyRedis::hashSet($hashKey, $data);
            MyRedis::hashInc($hashKey,'failnum',1);   // 发送失败次数加1
        }
    }

    /**
     * 发送验证码总接口(version 3.0)
     * @param $phone          手机号码
     * @param $identifyCode   验证码
     * @param $codetype       业务类型：0-注册帐号 1-发送验证码到旧绑定手机 2-更换绑定手机 3-提现 4-重置密码
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function newSendSMSVersion3($phone,$identifyCode,$codetype){
        $nums = self::updateSendNums($phone, $codetype);  // 获取3分钟内的点击次数

        $result = false;
        if ($nums % 2 == 0) {  // 偶数点击
            $result = self::sendSMSVersion3ByType($phone,$identifyCode,1);  // 电信短信发送接口
        }else{
            $result = self::sendSMSVersion3ByType($phone,$identifyCode,2);  // 互亿无线短信发送接口
        }
        return $result;
    }

    /**
     * @param $phone          手机号码
     * @param $identifyCode   验证码
     * @param $type           短信通道：1-电信  2-互亿无线  3-莫名
     * @return Boolean true：发送成功  false：发送失败
     */
    private static function sendSMSVersion3ByType($phone,$identifyCode,$type = 1){
        $result = false;
        if ($type != 2 && $type != 3) {  // 电信短信发送接口
            $result = YsSmSService::sendSms($phone,$identifyCode);
            self::recordSmsLog(1,$result);  // 记录电信发送情况
            $type = 2;
        }
        
        if ($type == 2 && !$result) {  // 互亿无线短信发送接口
            $result = HuyiSmsService::sendSms($phone,$identifyCode);
            self::recordSmsLog(2,$result);  // 记录互亿无线发送情况
            $type = 3;
        }
        
        if ($type == 3 && !$result) {  // 莫名短信发送接口
            $status = SMSService::sendSMS($phone,'您的验证码为：' . $identifyCode);
            if ($status == 100) $result = true;
            self::recordSmsLog(3,$result);  // 记录莫名发送情况
        }
        
        return $result;
    }

    /**
     * 获取某个手机号某种业务类型在一定时间内发送的次数，并加一
     * @param $phone          手机号码
     * @param $codetype       业务类型：0-注册帐号 1-发送验证码到旧绑定手机 2-更换绑定手机 3-提现 4-重置密码
     * @return int
     */
    private static function updateSendNums($phone, $codetype){
        $num = 0;
        if (MyRedis::exists("identifyCode:num:$codetype:$phone")) $num = intval(MyRedis::get("identifyCode:num:$codetype:$phone"));
        $expire = 3 * 60;  // 统计3分钟内的点击次数
        MyRedis::set("identifyCode:num:$codetype:$phone",($num + 1),$expire);  // 次数加1
        return $num;
    }

    /**
     * 短信内容发送总接口
     * 发发除了验证码之外的短信
     * @param String $phone   手机号码
     * @param int    $tid     模板ID：-1表示不使用模板，直接发送短信内容$content
     * @param Array  $params  模板参数：参数名param1、param2、param3、...
     * @param String $content 短信内容：模板ID为-1时才使用
     * @return Boolean true：发送成功  false：发送失败
     */
    public static function sendSmsContent($phone, $tid = -1, $params = array(), $content = null){
        $result = false;
        if ($tid != -1) {  // 按模板发送
            if (empty(self::TEMPLATE_SMS[$tid])) return $result;
            $contentArray = explode('【变量】', self::TEMPLATE_SMS[$tid]);
            if (count($contentArray) != count($params) + 1) return $result;

            $content_temp = '';
            foreach ($contentArray as $k => $v) {
                if ($k == 0) {
                    $content_temp = $v;
                }else{
                    $key = "param" . $k;
                    $content_temp .= $params[$key] . $v;
                }
            }
            
            // 优先调用互忆无线短信接口
            $result = HuyiSmsService::sendSmsContent($phone,$content_temp);
            self::recordSmsLog(2,$result,false);  // 记录互亿无线发送情况

            if (!$result) {  // 莫名短信发送接口
                $status = SMSService::sendSMS($phone,$content_temp);
                if ($status == 100) $result = true;
                self::recordSmsLog(3,$result,false);  // 记录莫名发送情况
            }
        }else{  // 直接发送短信内容：$content
            if (empty($content)) return $result;
            $status = SMSService::sendSMS($phone,$content);
            if ($status == 100) $result = true;
            self::recordSmsLog(3,$result,false);  // 记录莫名发送情况
        }
        return $result;
    }
}