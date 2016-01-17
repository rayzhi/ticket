<?php
namespace Common\Lib\Pclass;
defined( 'IN_HEAVEN' ) or die ( 'Hacking Attempt!' );
/**
 * 运行系统命令
 * 
 * PHP version 5
 * 
 * @category	COM
 * @package     GZSD
 * @version     SVN: $Id: Command.class.php 29 2013-10-21 08:00:46Z cengp $
 */

class Command {
	protected $_cmdMaps = array();
	
	protected $_cmdString = '';
	protected $_cmdStatus = '';
	protected $_cmdOutput = array();
	
	protected $_safeCMDs = array(
			'mysql', 'mysqladmin', 'mysqldump', 'tar', 'gzip',
			'ffmpeg', 'php', 'tail', 'svn', 'unzip', 'unrar',
			'xls2csv',
			);
	
	public function __construct($cmdMaps=array()){
		foreach($cmdMaps as $c=>$m){
			if(($t=substr($m, -1))=='/' || $t=='\\'){
				$cmdMaps[$c] = $m . $c;
			}
		}
		$this->_cmdMaps = $cmdMaps;
	}
	
	public function cmdString(){
		return $this->_cmdString;
	}
	
	public function cmdStatus(){
		return $this->_cmdStatus;
	}
	
	
	public function cmdOutput($returnArray=true){
		return $returnArray===true?$this->_cmdOutput:implode($returnArray, $this->_cmdOutput);
	}
	
	/**
	 * 运行命令
	 * @param string $cmd 命令
	 * @param bool $escape 是否转义
	 * @return bool 返回结果状态，成功为0，失败为1
	 */
	protected function _run_exec($cmd, $escape=true){
		$this->_cmdString = $escape?escapeshellcmd($cmd):$cmd;
		unset($this->_cmdOutput);
		exec($this->_cmdString, $this->_cmdOutput, $this->_cmdStatus);
		
		return $this->_cmdStatus;
	}
	
	protected function _run_system($cmd, $escape=true){
		$this->_cmdString = $escape?escapeshellcmd($cmd):$cmd;
		$this->_cmdOutput = system($this->_cmdString, $this->_cmdStatus);
		return $this->_cmdStatus;
	}
	
	protected function _run_popen($cmd, $escape=true, $mode='r', $background=false){
		$this->_cmdString = $escape?escapeshellcmd($cmd):$cmd;
		$pipe = popen($this->_cmdString , $mode);
		if (!$pipe) {
			$this->_cmdStatus = 0;
		}else{
			if(!$background){
				while(!feof($pipe)) {
					$this->_cmdOutput .= fread($pipe, 1024);
				}
			}
			pclose($pipe);
		}
		$this->_cmdStatus;
	}
	
	protected function _cmdCall($cmd, $arg='', $error2stdoutput=true, $background=false, $returnCMD=false){
		if(!empty($this->_cmdMaps[$cmd])){
			$realCMD = $this->_cmdMaps[$cmd];
		}elseif(in_array($cmd, $this->_safeCMDs)){
			$realCMD = $cmd;
		}else{
			throw new WebException("Unknown command: $cmd", SCRIPT_ERR_CONFIG);
		}
		
		if($arg){
			$realCMD .= " " . $arg;
		}
		
		// write error to stdouput
		if($error2stdoutput){
			$realCMD .= " 2>&1";
		}
		
		if($background){
			if(self::isWin()){
				$realCMD = 'start /b ' . $realCMD;
			}else{
				$realCMD .= " &";
			}
		}
		
		return $returnCMD?$realCMD:$this->_run_exec($realCMD, false);
	}
	
	public static function isWin(){
		return strtolower(substr(PHP_OS, 0, 3))=='win'?true:false;
	}
	
	/**
	 * __call魔术函数
	 *
	 * @param string $f 函数名
	 * @param array  $p 函数参数
	 */
	public function __call($f, $p)
	{
		$func = '_cmdCall';
		$cmd = $f;
		$arg = $p[0];
		
		return $this->$func($cmd, $arg);
	}
	
	protected function _getDBConf($db){
		$dbConf = SConfig::getDBConfig($db);
		
		if(strcasecmp($dbConf['driver'], 'mysql')){
			throw new __Exception("Not supported database type: {$dbConf['driver']}", SCRIPT_ERR_CONFIG);
		}
		
		return $dbConf;
	}
	
	public function createSetupDatabase($dbName){
		$dbConf = $this->_getDBConf('setup_db');
		
		$cmd = 'mysqladmin';
		$arg = "-u{$dbConf['user']} -p{$dbConf['password']} -h{$dbConf['host']} --default-character-set=utf8 --force create {$dbName}";
		
		return $this->$cmd($arg);
	}
	
	
	public function dumpSetupDatabase($dbName){
		$setupDBConf = $this->_getDBConf('setup_db');
		$demoDBConf = $this->_getDBConf('demo_db');
	
		$cmd = $this->_cmdCall("mysqldump", "-u{$demoDBConf['user']} -p{$demoDBConf['password']} -h{$demoDBConf['host']} --opt --quick --default-character-set=utf8 {$demoDBConf['database']}", true, false, true);
		$cmd .= " | " . $this->_cmdCall("mysql", "-u{$setupDBConf['user']} -p{$setupDBConf['password']} -h{$setupDBConf['host']} --batch --default-character-set=utf8 {$dbName}", true, false, true);

		// write error to stdouput
		//$cmd .= " 2>&1";
		
		return $this->_run_exec($cmd, false);
	}
	
	public function dropSetupDatabase($dbName){
		$dbConf = $this->_getDBConf('setup_db');
	
		$cmd = 'mysqladmin';
		$arg = "-u{$dbConf['user']} -p{$dbConf['password']} -h{$dbConf['host']} --default-character-set=utf8 --force drop {$dbName}";
	
		return $this->$cmd($arg);
	}
	
	/**
	 * AMR文件转MP3文件
	 * 
	 * @param string $amr_file AMR文件
	 * @param string $mp3_file MP3文件，不传或传空，则同AMR文件路径和文件名
	 * @return bool 成功为0，失败为1
	 */
	public function amr2mp3($amr_file, $mp3_file=NULL){
		if(!$mp3_file){
			$pathinfo = pathinfo($amr_file);
			$mp3_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.mp3';
		}
		$cmd = $this->_cmdCall("ffmpeg", "-i {$amr_file} {$mp3_file}", true, false, true);
		
		return $this->_run_exec($cmd, false);
	}
	
	/**
	 * 运行PHP程序
	 * 
	 * @param string $file PHP文件
	 * @param mixed $arg PHP参数
	 * @param boolean $background 是否后台运行，默认否
	 * @param string $output 日志文件，后台运行时，可以把结果输出到日志文件
	 * @param boolean $append 日志是覆盖还是追加，默认追加
	 */
	public function php($file, $arg=NULL, $background=false, $output=false, $append=true){
		if($arg && is_array($arg)){
			$arg_tmp = $arg;
			foreach($arg as $k=>$v){
				if(is_string($k)){
					$arg_tmp[] = "\"" . addcslashes($k, "\"") . "\"";
				}
				
				if(is_string($v)){
					$arg_tmp[] = "\"" . addcslashes($v, "\"") . "\"";
				}else{
					$arg_tmp[] = $v;
				}
			}
			
			$arg = implode(" ", $arg_tmp);
		}
		
		$arg = $file . " " . $arg;
		
		if($output){
			$arg .= " "  . ($append?" >> ":" > ") . $output;
		}
		
		$cmd = $this->_cmdCall("php", $arg, true, $background, true);
	
		if($background){
			if(self::isWin()){
				//return pclose(popen($cmd, "r"));
				$this->_run_popen($cmd, false, 'r', true);
				//return $this->_run_system($cmd, false);
			}else{
				return $this->_run_system($cmd, false);
			}
		}else{
			echo 123;
			return $this->_run_exec($cmd, false);
		}
	}
	
	/**
	 * 捕捉视频图像
	 * @param string $video_file 视频文件
	 * @param string $image_file 保存的图像文件
	 * @param string $start_time_offset 捕捉视频的时间点
	 * @return bool 成功为0，失败为1
	 */
	public function capture_screenshot($video_file, $image_file=NULL, $start_time_offset='00:00:01.01'){
		if(!$image_file){
			$pathinfo = pathinfo($video_file);
			$image_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.jpg';
		}
		
		$cmd = $this->_cmdCall("ffmpeg", "-i {$video_file} -ss {$start_time_offset} -f image2 -vframes 1 {$image_file}", true, false, true);
	
		return $this->_run_exec($cmd, false);
	}
	
	/**
	 * wmv转为mp4
	 * @param string $wmv_file wmv视频文件
	 * @param string $mp4_file 保存到mp4文件路径
	 * @return bool 成功为0，失败为1
	 */
	public function wmv2mp4($video_file, $mp4_file=NULL){
		if(!$mp4_file){
			$pathinfo = pathinfo($video_file);
			$mp4_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.mp4';
		}
	
		$cmd = $this->_cmdCall("ffmpeg", "-i {$video_file} -vcodec libx264 {$mp4_file}", true, false, true);
	
		return $this->_run_exec($cmd, false);
	}
	
	/**
	 * 返回文本文件最后几行内容
	 * 
	 * @param file $file 文件
	 * @param number $line 最好几行数
	 * @return string
	 */
	public function tail($file, $line=10){
		$cmd = $this->_cmdCall("tail", "-n {$line} {$file}", true, false, true);
		$this->_run_exec($cmd, false);
		return $this->cmdOutput("\n");
	}
	
	/**
	 * SVN
	 * @param string $command 命令参数
	 * @return string
	 */
	public function svn($command){
		$cmd = $this->_cmdCall("svn", $command, true, false, true);
		$this->_run_exec($cmd, false);
		return $this->cmdOutput("\n");
	}
	
	/**
	 * ZIP文件解压
	 * @param string $file 压缩文件
	 * @param string $todir 目标文件夹
	 * @return bool 成功为0，失败为1
	 */
	public function unzip($file, $todir=NULL){
		// overwrite existing files without prompting
		$args .= ' -o';
		$args .= ' ' . escapeshellarg($file);
		if($todir){
			$args .= ' -d ' . escapeshellarg($todir);
		}
		$cmd = $this->_cmdCall("unzip", $args, true, false, true);
		return $this->_run_exec($cmd, false);
	}
	
	/**
	 * RAR文件解压
	 * @param string $file 压缩文件
	 * @param string $todir 目标文件夹
	 * @return bool 成功为0，失败为1
	 */
	public function unrar($file, $todir=NULL){
		$args = ' e';
		// overwrite existing files without prompting
		$args .= ' -o+';
		$args .= ' ' . escapeshellarg($file);
		if($todir){
			$args .= ' ' . escapeshellarg($todir);
		}
		$cmd = $this->_cmdCall("unrar", $args, true, false, true);
		return $this->_run_exec($cmd, false);
	}
	
	/**
	 * 自动判断类型解压文件
	 * @param string $file 压缩文件
	 * @param string $todir 目标文件夹
	 * @return bool 成功为0，失败为1
	 */
	public function decompress($file, $todir=NULL){
		$info = pathinfo($file);
		if(!$info || !$info['extension']){
			return false;
		}
		
		switch(strtolower($info['extension'])){
			case 'zip':
				return $this->unzip($file, $todir);
				break;
				
			case 'rar':
				return $this->unrar($file, $todir);
				break;
			
			default:
				return false;
		}
	}
	
	/**
	 * Excel97文件转为CSV格式
	 * @param string $fromfile 源Excel文件
	 * @param string $tofile 目标CSV文件
	 * @param string $fromcharset 源字符集，UTF-8， BIG5 ，注意，简体中文GBK是：cp936，可用命令：xls2csv -s 查看支持的字符集
	 * @param string $tocharset 目标字符集
	 * @return bool 成功为0，失败为1
	 */
	public function xls2csv($fromfile, $tofile, $fromcharset='UTF-8', $tocharset='UTF-8'){
		$args = ' -x' . escapeshellarg($fromfile);
		$args .= ' -c' . escapeshellarg($tofile);
		$args .= ' -b ' . $fromcharset;
		$args .= ' -a ' . $tocharset;
		
		$cmd = $this->_cmdCall("xls2csv", $args, true, false, true);
		return $this->_run_exec($cmd, false);
	}
}