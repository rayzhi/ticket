<?php 
/**
 * 调试类
 * 
 * PHP version 5
 * 
 * @category	Plcass
 * @package     Lib
 * @subpackage  Class
 * @version     SVN: $Id: Webown.class.php 10 2013-10-08 01:34:05Z Cengp $
 */
namespace Common\Lib\Pclass;
class Webown {
	
	/**
	 * 运行后台任务
	 * @param string $module 任务模块
	 * @param string $action 任务方法
	 * @param mixed $args 任务参数
	 * @return bool
	 */
	public static function run_cron($module, $action, $args=NULL){
		$cmd_args = array();
		
		// 后台运行日志
		$log_file = LOG_PATH . 'cron_' . date('Ymd') . '.log';
		$tmp_log_file = tempnam(sys_get_temp_dir, "cron");
		
		// 'CRON_URL_MODEL'	=>	1, // 1 (PATHINFO 模式), 采用 index.php module/action/id/4, 其它 URL模式 采用 index.php module action id 4
		if(C('CRON_URL_MODEL')==1){
			$cmd_args[] = rawurlencode($module);
			$cmd_args[] = rawurlencode($action);
			
			if($args){
				if(is_array($args)){
					foreach($args as $k=>$v){
						$cmd_args[] = rawurlencode($k);
						$cmd_args[] = rawurlencode($v);
					}
				}else{
					$cmd_args[] = $args;
				}
			}
			
			$cmd_args[] = rawurlencode('_cron_log_file');
			$cmd_args[] = rawurlencode($log_file);
			$cmd_args[] = rawurlencode('_cron_tmp_file');
			$cmd_args[] = rawurlencode($tmp_log_file);
			
			$cmd_args = implode('/', $cmd_args);
		}else{

			$cmd_args[] = $module;
			$cmd_args[] = $action;
			
			if($args){
				$cmd_args[] = $args;
			}
			
			$cmd_args['_cron_log_file'] = $log_file;
			$cmd_args['_cron_tmp_file'] = $tmp_log_file;
		}
		
		$oCommand = new Command(C('BIN_MAPS'));
		$status = $oCommand->php(APP_ROOT . 'cron/cron.php', $cmd_args, true, $tmp_log_file, false);
		
		return $status==0?true:false;
	}
	
	

	/**
	 * 调试日志
	 * @param mixed $info
	 */
	public static function debug_log($info, $file=NULL){
		check_dir(RUNTIME_PATH . 'Logs/debug');
		file_put_contents(RUNTIME_PATH . 'Logs/debug/'.'debug_' . date('Ymd') . '.log', "[" . date('H:i:s') . "] " . $info . "\n", FILE_APPEND);
	}
	
	/**
	 * 根据不同模式存放不同的日志
	 * @param int type 例如：sale,pay...
	 */
	public static function record_log($info, $type=NULL){
		$type = $type ? $type : 'debug';
		check_dir(RUNTIME_PATH . 'Logs/'.$type);
		file_put_contents(RUNTIME_PATH . 'Logs/'.$type.'/'.''.$type.'_' . date('Ymd') . '.log', "[" . date('H:i:s') . "] " . $info . "\n", FILE_APPEND);
	}
	
	
}