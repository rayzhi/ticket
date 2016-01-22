<?php
namespace Common\Lib\phpqrcode;
require_once "phpqrcode.php";
use Think\Model;
use Think\Log;
class Qrcode {
    
    private $_option = array();
    
    public function __construct(){
        $_option=array(
                 "basePath"=>$_SERVER['DOCUMENT_ROOT'],
                 "relativePath"=>C('ERWEIMA_PATH'),
                 "errorCorrectionLevel"=>"L",//// 纠错级别：7%L、15%M、25%Q、30%H--字碼可被修正
                 "matrixPointSize"=>4,//100x100--5:125--6:150.....(max10)
                 "margin"=>2,
                
         );
        $this->setConfig($_option);
    }
    
    /*
    $data 数据
     $filename 保存的图片名称
     $errorCorrectionLevel 错误处理级别
     $matrixPointSize 每个黑点的像素
     $margin 图片外围的白色边框像素
    */
    public function createQRcode($data=''){
        if(!$data){return false;}
        $options = $this->_option;
        $PNG_TEMP_DIR = $options['basePath'].$options['relativePath'].date('Y-m-d').'/';
        $urlPath = 'http://'.$_SERVER['HTTP_HOST'].$options['relativePath'].date('Y-m-d').'/';
        if(!is_dir($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR,0777,true);//php 5
        }
        $errorCorrectionLevel = $options['errorCorrectionLevel'];
        $matrixPointSize = $options['matrixPointSize'];
        $margin = $options['margin'];
        $qrname = $this->getNewFileName($data);
        $qrcodePath = $options['relativePath'].'/'.$qrname;
        $filename = $PNG_TEMP_DIR.$qrname;
        $urlPath = $urlPath.$qrname;
        \QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, $margin); 
        return $urlPath;
    }
    
    /*** 设置配置选项* * @param $option* @return void*/
    
    public function setConfig($option){ 
        foreach($option as $key=>$val){
            $this->_option[$key]=$val;
        } 
    }
    /*获取文件名*/
    public function getNewFileName($data){
        $ecl = $this->_option['errorCorrectionLevel'];
        $mps = $this->_option['matrixPointSize'];
        $md5 = md5($data.'|'.$ecl.'|'.$mps);
        $extName = '.png';
        return substr($md5,8,16).rand(1000,10000).$extName;
    }
    
}