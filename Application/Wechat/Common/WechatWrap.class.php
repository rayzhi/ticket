<?php

namespace Wechat\Common; 
use Wechat\Lib\Wechat;
use Wechat\Handler as h;

class WechatWrap
{
    //获取微信操作实例
    public static function getInstance(){
        $config = array(
            'token'=>C('WECHAT_TOKEN'),
            'encodingaeskey'=>C('WECHAT_ENCODINGAESKEY'),
            'appid'=>C('WECHAT_APPID'),
            'appsecret'=>C('WECHAT_APPSECRET'));
        $weObj = new Wechat($config);
        return $weObj;
    }

    //处理微信请求
    public static function handleMsg($msg){
        $filenames = self::read_all_dir("./Application/Wechat/Handler");
        $msgtype = $msg->getRevType();
        //遍历每个handler，并处理各种类型的微信数据
        foreach ($filenames as $filename) {
            $result = "";
            preg_match($rule, $filename,$result);
            $classname = basename($filename,"Handler.class.php");
            file_put_contents("./test.txt", $classname);
            $handler = self::newObj($classname,"Handler");
            switch ($msgtype) {
                case Wechat::MSGTYPE_TEXT:
                    $isnext = $handler->handleText($msg);
                   break;
                case Wechat::MSGTYPE_IMAGE:
                    $isnext = $handler->handleImage($msg);
                   break;
                case Wechat::MSGTYPE_LOCATION:
                    $isnext = $handler->handleLocation($msg);
                   break;
                case Wechat::MSGTYPE_LINK:
                    $isnext = $handler->handleLink($msg);
                   break;
                case Wechat::MSGTYPE_EVENT:
                    $isnext = $handler->handleEvent($msg);
                   break;
                case Wechat::MSGTYPE_MUSIC:
                    $isnext = $handler->handleMusic($msg);
                   break;
                case Wechat::MSGTYPE_NEWS:
                    $isnext = $handler->handleNews($msg);
                   break;
                case Wechat::MSGTYPE_VOICE:
                    $isnext = $handler->handleVoice($msg);
                   break;
                case Wechat::MSGTYPE_VIDEO:
                    $isnext = $handler->handleVideo($msg);
                   break;
                default:
                    echo " ^6^别蒙我，你可不是微信的请求。";
                    break;
            }
            if(!$isnext) return;     //如果不继续，就直接返回.
        }
        
    }


    //读取文件夹里面的文件名
    private static function read_all_dir ( $dir ){
        $result = array();
        $handle = opendir($dir);
        if ( $handle ){
            while ( ( $file = readdir ( $handle ) ) !== false ){
                if ( $file != '.' && $file != '..'){
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if ( is_dir ( $cur_path ) ){
                        $result['dir'][$cur_path] = read_all_dir ( $cur_path );
                    }
                    else{
                        $result['file'][] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result['file'];
    }

    //类似D函数的功能
    private static function newObj($name,$layer){
        static $_obj = array();
        $class = parse_res_name($name,$layer);
        if(isset($_obj[$name.$layer])){
            return $_obj[$name.$layer];
        }
        $class = parse_res_name($name,$layer);
        if(class_exists($class)) {
            $model = new $class($name);
        }
        $_obj[$name.$layer]  =  $model;
        return $model;
    }
}