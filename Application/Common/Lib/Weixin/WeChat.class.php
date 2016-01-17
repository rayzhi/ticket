<?php 
/**
 * 微信基础公共类
 * 
 * PHP version 5
 * 
 * @category	Plcass
 * @package     Lib
 * @subpackage  Class
 * @version     SVN: $Id: Wxconfig.class.php 10 2015-02-06 16:29:05Z Cengp $
 */
namespace Common\Lib\Weixin;

class WeChat {
	
	/**
	 * 调试模式，将错误通过文本消息回复显示
	 *
	 * @var boolean
	 */
	private $debug;
	
	/**
	 * 以数组的形式保存微信服务器每次发来的请求
	 *
	 * @var array
	 */
	private $request;
	
	/**
	 * 初始化，判断此次请求是否为验证请求，并以数组形式保存
	 *
	 * @param string $token 验证信息
	 * @param boolean $debug 调试模式，默认为关闭
	 */
	public function __construct($token, $debug = FALSE) {
		if (!$this->validateSignature($token)) {
			exit('签名验证失败');
		}
	
		if ($this->isValid()) {
			// 网址接入验证
			exit($_GET['echostr']);
		}
	
		if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			exit('缺少数据');
		}
	
		$this->debug = $debug;
		set_error_handler(array(&$this, 'errorHandler'));
		// 设置错误处理函数，将错误通过文本消息回复显示
	
		$xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
	
		$this->request = array_change_key_case($xml, CASE_LOWER);
		// 将数组键名转换为小写，提高健壮性，减少因大小写不同而出现的问题
	}
	
	/**
	 * 判断此次请求是否为验证请求
	 *
	 * @return boolean
	 */
	private function isValid() {
		return isset($_GET['echostr']);
	}
	
	/**
	 * 验证此次请求的签名信息
	 *
	 * @param  string $token 验证信息
	 * @return boolean
	 */
	private function validateSignature($token) {
		if ( ! (isset($_GET['signature']) && isset($_GET['timestamp']) && isset($_GET['nonce']))) {
			return FALSE;
		}
	
		$signature = $_GET['signature'];
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];
	
		$signatureArray = array($token, $timestamp, $nonce);
		sort($signatureArray,SORT_STRING);
	
		return sha1(implode($signatureArray)) == $signature;
	}
	
	/**
	 * 获取本次请求中的参数，不区分大小
	 *
	 * @param  string $param 参数名，默认为无参
	 * @return mixed
	 */
	protected function getRequest($param = FALSE) {
		if ($param === FALSE) {
			return $this->request;
		}
	
		$param = strtolower($param);
	
		if (isset($this->request[$param])) {
			return $this->request[$param];
		}
	
		return NULL;
	}
	
	/**
	 * 用户关注时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onSubscribe() {}
	
	/**
	 * 用户取消关注时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onUnsubscribe() {}
	
	/**
	 * 收到文本消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onText() {}
	
	/**
	 * 收到图片消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onImage() {}
	
	/**
	 * 收到地理位置消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onLocation() {}
	
	/**
	 * 收到链接消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onLink() {}
	
	/**
	 * 收到自定义菜单消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onClick() {}
	
	/**
	 * 收到地理位置事件消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onEventLocation() {}
	
	/**
	 * 收到语音消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onVoice() {}
	
	/**
	 * 扫描二维码时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onScan() {}
	
	/**
	 * 收到未知类型消息时触发，用于子类重写
	 *
	 * @return void
	 */
	protected function onUnknown() {}
	
	public static function get_request_url(){
		$protocol = !empty($_SERVER['HTTPS'])?'https':'http';
		$host = !empty($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(!empty($_SERVER['SERVER_NAME'])?$_SERVER['SCRIPT_NAME']:$_SERVER['SERVER_ADDR']);
		$port = $_SERVER['SERVER_PORT'];
		$request = $_SERVER['REQUEST_URI'];
	
		$url = $protocol . "://" . $host . ((($protocol=='https' && $port!=443) || ($protocol=='http' && $port!=80))?$port:'') . $request;
	
		return $url;
	}
	
	protected function _log($info, $file=NULL){
		check_dir(RUNTIME_PATH . 'Logs/Wechat');
		file_put_contents($file?$file:(RUNTIME_PATH . 'Logs/Wechat/'.'debug_' . date('Ymd') . '.log'), "[" . date('H:i:s') . "] " . ( (is_array($info) || is_object($info)) ?print_r($info, true):$info ) . "\n" , FILE_APPEND);
	}
}