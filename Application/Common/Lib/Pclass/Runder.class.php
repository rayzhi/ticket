<?php 
namespace Common\Lib\Pclass;
use Common;
defined('IN_HEAVEN') or die('Hacking Attempt!');
/**
 * 运行容器
 * 
 * PHP version 5
 * 
 * @category	Pclass
 * @package     GZSD
 * @version     SVN: $Id: Runder.class.php 29 2013-10-21 08:00:46Z Cengp $
 */

class Runder {
	private static $_instance = array();
	protected static $_autoload_paths = array();
	protected static $_shutdown_functions = array();
	
	public static function add_autoload_path($path, $ext='.class.php'){
		self::$_autoload_paths[$path] = $ext;
	}
	
	public static function autoload($class){
		foreach(self::$_autoload_paths as $path => $ext){
			if(import($class, $path, $ext)){
				return;
			}
		}
	}
	
	public static function reautoload($paths = array(), $ext='.class.php'){

		if(is_string($paths)){
			$paths = array($paths);
		}
		foreach($paths as $path){
			self::add_autoload_path($path, $ext);
		}
		
		
		// unregister old
		//spl_autoload_unregister(array('Think', 'autoload'));
		
		/*
		 Array ( [0] => Array ( 
		 [0] => Think [1] => autoload ) 
		 [1] => Array ( [0] => (object) [1] => autoload )  
		 [2] => import ) 
		*/
		// check loaded?
		if($aLoadedFuncs = spl_autoload_functions()){
			foreach($aLoadedFuncs as $f){
				if($f && is_array($f) && is_string($f[0]) && $f[0]==__CLASS__ && $f[1]=='autoload'){
					return;
				}
			}
		}
		
		// register Runder::autoload
		spl_autoload_register(array(__CLASS__, 'autoload'));	
	}
	
	/**
	 * 取得对象实例 支持调用类的静态方法
	 * @param string $class 对象类名
	 * @param string $method 类的静态方法名
	 * @return object
	 */
	public static function instance($class, $args=array()) {
		$identify   =   $class.md5(serialize($args));
		if(!isset(Runder::$_instance[$identify])) {
			
			if(!class_exists($class)){
				$arg_str = '';
				if($args && is_array($args)){
					foreach ($args as $i=>$arg){
						if(is_object($arg) || is_array($arg)){
							$arg_param_name = 'arg_param' . $i;
							$$arg_param_name = $arg;
							$arg_str .= ", \${$arg_param_name}";
						}else{
							$arg_str .= ", '" . addcslashes($arg, "'") . "'";
						}
					}
						
					if($arg_str){
						$arg_str = substr($arg_str, 2);
					}
						
				}elseif($args && is_object($args)){
					$arg_param_name = 'arg_param';
					$$arg_param_name = $args;
					$arg_str = "\${$arg_param_name}";
						
				}elseif($args){
					$arg_str = "'" . addcslashes($args, "'") . "'";
				}
				require_once APP_ROOT.'apps/Common/Lib/Pclass/'.$class . '.class.php';
				$code = "return new " . $class . "(" . $arg_str . ");";
				
				echo "code==$code";exit;
				
				$o = eval($code);
	
				if(!$o){
					echo "Cann't init class instanse: $class";
				}
				Runder::$_instance[$identify] = $o;
			}else{
				echo L('_CLASS_NOT_EXIST_').':'.$class;
			}
		}
		return Runder::$_instance[$identify];
	}
	
	/**
	 * 注册shutdown函数
	 * 
	 * @param mixed $callback 回调函数
	 * @param array $params 回调参数
	 * @param int $runtimes 运行次数  小于0，表示不限次数，0=删除之前注册的$callback函数，1=只运行一次，2=运行两次。。。。
	 */
	public static function register_shutdown_function($callback, $params=NULL, $runtimes=NULL){
		static $_registered = false;
		
		if(empty($callback)){
			return;
		}
		
		if($runtimes===0){
			foreach (self::$_shutdown_functions as $k=>$cb){
				if($cb[0]===$callback){
					unset(self::$_shutdown_functions[$k]);
				}
			}
		}elseif($runtimes>0){
			$exist = 0;
			foreach (self::$_shutdown_functions as $k=>$cb){
				if($cb[0]===$callback){
					$exist++;
				}
			}
			
			if($exist<$runtimes){
				self::$_shutdown_functions[] = array($callback, $params);
			}
		}else{
			self::$_shutdown_functions[] = array($callback, $params);
		}
		
		if(!$_registered && self::$_shutdown_functions){
			register_shutdown_function(array('Runder', 'run_shutdown_function'));
			$_registered = true;
		}	
	}
	
	/**
	 * 运行回调函数
	 */
	public static function run_shutdown_function(){
		if(self::$_shutdown_functions){
			foreach(self::$_shutdown_functions as $cb){
				call_user_func_array($cb[0], $cb[1]?$cb[1]:array());
			}
		}
	}
	
	public static function throw_exception($message, $code=NULL, $data=NULL, $file=NULL, $line=NULL){
		if(!class_exists('WebException')){
			import('COM.GZNC.WebException');
		}
		throw new WebException($message, $code, $data, $file, $line);
	}
	
	/**
	 * 运行回调函数
	 *
	 * 回调函数支持以下几种格式：
	 * 1、直接函数：funcname，或带参数：array(funcname, params)
	 * 2、静态方法：array(array('WeixinApi', 'methodname'), params)
	 * 3、对象方法：array(Object, 'methodname') 或  array(array(Object, 'methodname'), params)
	 * 4、二次回调，如：
	 * array(array(
	 array(array('WeixinApi', 'instance'), 'S4WeixinResponse')
	 , 'run')
	 , '')
	 	
	 可以先调用Runder::instance()初始化S4Web实例后，再调用S4Web->apiglog_save()方法执行回调
	 *
	 * @param mixed $callback 回调函数
	 * @param array $extra_params 回调参数
	 * @param mixed $callbackObject 回调对象，可以通过此参数获取到回调对象
	 * @return mixed
	 */
	public function run_callback($callback, $extra_params=array(), &$callbackObject=NULL) {
		$extra_params = is_array ( $extra_params ) ? $extra_params : ($extra_params ? array (
				$extra_params
		) : array ());
	
		$params = $extra_params;
	
		if(is_object($callback)){
			return self::throw_exception(
					"Object callback must set method"
					, SCRIPT_ERR_CONFIG
					, array('callback'=>$callback)
					, __FILE__, __LINE__
			);
		}
		else if (is_array ( $callback )) {
			$func = $callback [0];
			if (! empty ( $callback [1] )) {
				if (is_array ( $callback [1] )) {
					$params = array_merge ( $extra_params, $callback [1] );
				} else {
					$params [] = $callback [1];
				}
			}
				
			if (is_object ( $func )) {
				$callbackObject = $func;
				// 注意：此处不需要传$params作为参数
				return call_user_method_array ( $callback [1], $callback [0], $extra_params );
			} elseif (is_object ( $callback [0] [0] )) {
				$callbackObject = $callback [0] [0];
				return call_user_method_array ( $callback [0] [1], $callback [0] [0], $params);
			}
		} else {
			$func = $callback;
		}
	
		if(is_array($func) && is_array($func[0])){
			$func = array(call_user_func_array($func[0][0], is_array($func[0][1])?$func[0][1]:array($func[0][1])), $func[1]);
		}
	
	
		if(is_array($func) && is_object($func[0])){
			$callbackObject = $func[0];
		}
	
		return call_user_func_array ( $func, $params);
	}
	
	/**
	 * Provides central static variable storage.
	 *
	 * All functions requiring a static variable to persist or cache data within
	 * a single page request are encouraged to use this function unless it is
	 * absolutely certain that the static variable will not need to be reset during
	 * the page request. By centralizing static variable storage through this
	 * function, other functions can rely on a consistent API for resetting any
	 * other function's static variables.
	 *
	 * Example:
	 * @code
	 * function language_list($field = 'language') {
	 *   $languages = &drupal_static(__FUNCTION__);
	 *   if (!isset($languages)) {
	 *     // If this function is being called for the first time after a reset,
	 *     // query the database and execute any other code needed to retrieve
	 *     // information about the supported languages.
	 *     ...
	 *   }
	 *   if (!isset($languages[$field])) {
	 *     // If this function is being called for the first time for a particular
	 *     // index field, then execute code needed to index the information already
	 *     // available in $languages by the desired field.
	 *     ...
	 *   }
	 *   // Subsequent invocations of this function for a particular index field
	 *   // skip the above two code blocks and quickly return the already indexed
	 *   // information.
	 *   return $languages[$field];
	 * }
	 * function locale_translate_overview_screen() {
	 *   // When building the content for the translations overview page, make
	 *   // sure to get completely fresh information about the supported languages.
	 *   drupal_static_reset('language_list');
	 *   ...
	 * }
	 * @endcode
	 *
	 * In a few cases, a function can have certainty that there is no legitimate
	 * use-case for resetting that function's static variable. This is rare,
	 * because when writing a function, it's hard to forecast all the situations in
	 * which it will be used. A guideline is that if a function's static variable
	 * does not depend on any information outside of the function that might change
	 * during a single page request, then it's ok to use the "static" keyword
	 * instead of the drupal_static() function.
	 *
	 * Example:
	 * @code
	 * function actions_do(...) {
	 *   // $stack tracks the number of recursive calls.
	 *   static $stack;
	 *   $stack++;
	 *   if ($stack > variable_get('actions_max_stack', 35)) {
	 *     ...
	 *     return;
	 *   }
	 *   ...
	 *   $stack--;
	 * }
	 * @endcode
	 *
	 * In a few cases, a function needs a resettable static variable, but the
	 * function is called many times (100+) during a single page request, so
	 * every microsecond of execution time that can be removed from the function
	 * counts. These functions can use a more cumbersome, but faster variant of
	 * calling drupal_static(). It works by storing the reference returned by
	 * drupal_static() in the calling function's own static variable, thereby
	 * removing the need to call drupal_static() for each iteration of the function.
	 * Conceptually, it replaces:
	 * @code
	 * $foo = &drupal_static(__FUNCTION__);
	 * @endcode
	 * with:
	 * @code
	 * // Unfortunately, this does not work.
	 * static $foo = &drupal_static(__FUNCTION__);
	 * @endcode
	 * However, the above line of code does not work, because PHP only allows static
	 * variables to be initializied by literal values, and does not allow static
	 * variables to be assigned to references.
	 * - http://php.net/manual/language.variables.scope.php#language.variables.scope.static
	 * - http://php.net/manual/language.variables.scope.php#language.variables.scope.references
	 * The example below shows the syntax needed to work around both limitations.
	 * For benchmarks and more information, see http://drupal.org/node/619666.
	 *
	 * Example:
	 * @code
	 * function user_access($string, $account = NULL) {
	 *   // Use the advanced drupal_static() pattern, since this is called very often.
	 *   static $drupal_static_fast;
	 *   if (!isset($drupal_static_fast)) {
	 *     $drupal_static_fast['perm'] = &drupal_static(__FUNCTION__);
	 *   }
	 *   $perm = &$drupal_static_fast['perm'];
	 *   ...
	 * }
	 * @endcode
	 *
	 * @param $name
	 *   Globally unique name for the variable. For a function with only one static,
	 *   variable, the function name (e.g. via the PHP magic __FUNCTION__ constant)
	 *   is recommended. For a function with multiple static variables add a
	 *   distinguishing suffix to the function name for each one.
	 * @param $default_value
	 *   Optional default value.
	 * @param $reset
	 *   TRUE to reset a specific named variable, or all variables if $name is NULL.
	 *   Resetting every variable should only be used, for example, for running
	 *   unit tests with a clean environment. Should be used only though via
	 *   function drupal_static_reset() and the return value should not be used in
	 *   this case.
	 *
	 * @return
	 *   Returns a variable by reference.
	 *
	 * @see drupal_static_reset()
	 */
	public static function &register_static($name, $default_value = NULL, $reset = FALSE) {
		static $data = array(), $default = array();
		
		// First check if dealing with a previously defined static variable.
		if (isset($data[$name]) || array_key_exists($name, $data)) {
			// Non-NULL $name and both $data[$name] and $default[$name] statics exist.
			if ($reset) {
				// Reset pre-existing static variable to its default value.
				$data[$name] = $default[$name];
			}
			return $data[$name];
		}

		// Neither $data[$name] nor $default[$name] static variables exist.
		if (isset($name)) {
			if ($reset) {
				// Reset was called before a default is set and yet a variable must be
				// returned.
				return $data;
			}
			// First call with new non-NULL $name. Initialize a new static variable.
			$default[$name] = $data[$name] = $default_value;
			return $data[$name];
		}
		// Reset all: ($name == NULL). This needs to be done one at a time so that
		// references returned by earlier invocations of drupal_static() also get
		// reset.
		foreach ($default as $name => $value) {
			$data[$name] = $value;
		}

		// As the function returns a reference, the return should always be a
		// variable.
		return $data;
	}
	
	/**
	 * Resets one or all centrally stored static variable(s).
	 *
	 * @param $name
	 *   Name of the static variable to reset. Omit to reset all variables.
	 */
	public static function reset_static($name = NULL) {
		self::register_static($name, NULL, TRUE);
	}
}