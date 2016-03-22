<?php

function createQr($strcon){
	//$qr = new \Common\Lib\phpqrcode\Qrcode();
	//return $qr->createQRcode($strcon);
	return "http://qr.liantu.com/api.php?text=".$strcon;
}


function slog($content){
	file_put_contents("./test.log",$content,FILE_APPEND);
}


/**
 * URL添加参数
 * @param string $url
 * @param mixed $param
 * @return string
 */
function url_append_param($url, $param){
	if(empty($param)){
		return $url;
	}

	$aParse = parse_url($url);
	$aParams = array();
	parse_str($aParse['query'], $aParams);

	$aNewParams = array();
	if(is_string($param)){
		parse_str($param, $aNewParams);
	}else{
		$aNewParams = $param;
	}

	$aNewParams = array_merge($aParams, $aNewParams);

	$link = ($aParse['scheme']?$aParse['scheme'].'://':'')
	. $aParse['host']
	. $aParse['path']
	. '?' . http_build_query($aNewParams)
	. ($aParse['fragment']?'#' . $aParse['fragment']:'');

	return $link;
}

/**
 *  获取回调url
 *
 *  @param string $default 默认跳转链接
 *  @return string
 *  @access public
 */
function get_redirect($default='')
{
	$rdParam = C('VAR_REDIRECT');
	if (!empty($_POST[$rdParam])){
		$redirect = $_POST[$rdParam];
	}
	else if (!empty($_GET[$rdParam])){
		$redirect = $_GET[$rdParam];
	}
	else if($default){
		$redirect = $default;
	}
	/*
	 elseif ($_SERVER['HTTP_REFERER'])
		{
	$redirect = $_SERVER['HTTP_REFERER'];
	}
	*/
	else{
		$redirect = 'http://' . $_SERVER['SERVER_NAME'];
	}

	if ((isset($_REQUEST['paramEncode']) && $_REQUEST['paramEncode']=='js')

	|| (strtolower($_REQUEST['encoding'])=='utf8' || strtolower($_REQUEST['encoding'])=='utf-8') );
	{
		if(isset($_REQUEST['paramEncode']))  unset($_REQUEST['rdEncode']);
		if(isset($_REQUEST['encoding'])) unset($_REQUEST['encoding']);
		//$redirect = Coder::unescape($redirect);
	}

	return $redirect;
}

/**
 *  翻译字符串到当前或指定语言
 * @return
 *   The translated string.
 *
 * @see st()
 * @see get_t()
 * @see format_string()
 * @ingroup sanitization
 */
function TL($string, array $args = array(), array $options = array()) {
	return $string;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 去掉HTML标签
 * @param $content 带Html标签的内容
 * @return 不带Html标签的内容
 */
function stripHtmlTags($content){
	$content = htmlspecialchars_decode($content);
	$specialTags = array("&nbsp;","<p>","</p>","<br />");  // 特殊的HTML标签
	$replaceTags = array(" ","","","");
	$content = strip_tags($content);  // 去掉HTML标签
	return str_replace($specialTags, $replaceTags, $content);
}

/**
 * 计算前端显示的评论时间
 * @param $time 从1970年1月1日0:0:0开始计算的时间(秒)
 * @return string
 */
function convertCommentTime($time){
	$currentTime = time();
	$timeSegment = array(1,60,3600,3600*24,3600*24*30,3600*24*365,PHP_INT_MAX);   // 时间区间段:秒、分、小时、天、月、年
	$name = array('秒前','分钟前','小时前','天前','个月前','年前'); 
	$diff = $currentTime - $time;
	if ($diff <= 0) $diff = 1;    // 一般来说，$time比当前时间小，否则当作1秒来计算
	$index = -1;
	for($i = 0; $i < count($timeSegment) - 1; ++$i){
		if ($diff >= $timeSegment[$i] && $diff < $timeSegment[$i+1]) {
			$index = $i;
			break;
		}
	}

	if ($index == -1) $index = count($timeSegment) - 2;
	
	// 若1个月~2个月算1个月之前：使用floor函数；若1个月~2个月算2个月之前：使用ceil函数
	$result = floor($diff/$timeSegment[$index]) . $name[$index];
	return $result;
}

/**
 * 根据用户ID生成用户凭证(userToken)
 * 生成规则：md5(32位随机数 + 11位用户ID + time() + rand(11111,99999))
 * @param $userId 用户ID
 * @return string 用户凭证
 */
 function generateUserToken($userId){
 	// 32位随机数
 	mt_srand((double)microtime() * 1000000);  // 设置随机种子
 	$chars = array(
 		'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
 		'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
 		'/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
 		'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
 		'?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
 		'=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
 		'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
 	);
 	$numChars = count($chars);
 	$token = '';
 	for ($i=0; $i<32; $i++)
 		$token .= $chars[mt_rand(0,$numChars-1)];

 	$idStr = '';
 	if ($userId == -1) {  // 超级用户ID：-1(程序内部推送消息需要用到)
 		$idStr = '99999999999';
 	}else{
 		// 11位用户ID
	 	$zero = '00000000000';
	 	$userIdLen = strlen(strval($userId));
	 	$idStr = substr($zero, $userIdLen, strlen($zero) - $userIdLen);
	 	$idStr = $idStr . $userId;
 	}

 	$time = time();
 	$randNum = rand(11111,99999);
 	$md5Token = md5($token . $idStr . $time . $randNum);
 	return $md5Token;
 }

/**
 * 对用户明文密码加密
 * 在用户表ts_user中增加login_salt字段保存每个用户独有的md5盐值(其在注册时随机生成rand(11111,99999))
 * @param $password  用户明文密码
 * @param $salt      md5加密盐值
 * @return String    加密后的密码
 */
function encryptPassword($password,$salt){
	return md5(md5($password) . $salt);
}

/**
 * 生成GUID码
 * @param $str  需要排序的字符串
 * @return String
 */
function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);  // chr(45):"-"    chr(123):"{"     chr(125):"}"
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
        return strtolower($uuid);
    }
}

/**
 * 对字符串从小到大排序并将其转换为大写字母串(目前主要用于统计用户答题情况)
 * @param $str  需要排序的字符串
 * @return String
 */
 function sortAndToUpper($str){
 	$str = strtoupper($str);
 	$tmpArray = str_split($str);
 	sort($tmpArray);
 	$str = implode('',$tmpArray);
 	return $str;
 }

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function UC($url='',$vars='',$suffix=true,$domain=false) {
	// 解析URL
	$info   =  parse_url($url);
	$url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
	if(isset($info['fragment'])) { // 解析锚点
		$anchor =   $info['fragment'];
		if(false !== strpos($anchor,'?')) { // 解析参数
			list($anchor,$info['query']) = explode('?',$anchor,2);
		}
		if(false !== strpos($anchor,'@')) { // 解析域名
			list($anchor,$host)    =   explode('@',$anchor, 2);
		}
	}elseif(false !== strpos($url,'@')) { // 解析域名
		list($url,$host)    =   explode('@',$info['path'], 2);
	}
	// 解析子域名
	if(isset($host)) {
		$domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
	}elseif($domain===true){
		$domain = $_SERVER['HTTP_HOST'];
		if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
			$domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
			// '子域名'=>array('模块[/控制器]');
			foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
				$rule   =   is_array($rule)?$rule[0]:$rule;
				if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
					$domain = $key.strstr($domain,'.'); // 生成对应子域名
					$url    =  substr_replace($url,'',0,strlen($rule));
					break;
				}
			}
		}
	}

	// 解析参数
	if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
		parse_str($vars,$vars);
	}elseif(!is_array($vars)){
		$vars = array();
	}
	if(isset($info['query'])) { // 解析地址里面参数 合并到vars
		parse_str($info['query'],$params);
		$vars = array_merge($params,$vars);
	}

	// URL组装
	$depr       =   C('URL_PATHINFO_DEPR');
	$urlCase    =   C('URL_CASE_INSENSITIVE');
	if($url) {
		if(0=== strpos($url,'/')) {// 定义路由
			$route      =   true;
			$url        =   substr($url,1);
			if('/' != $depr) {
				$url    =   str_replace('/',$depr,$url);
			}
		}else{
			if('/' != $depr) { // 安全替换
				$url    =   str_replace('/',$depr,$url);
			}
			// 解析模块、控制器和操作
			$url        =   trim($url,$depr);
			$path       =   explode($depr,$url);
			$var        =   array();
			$varModule      =   C('VAR_MODULE');
			$varController  =   C('VAR_CONTROLLER');
			$varAction      =   C('VAR_ACTION');
			$var[$varAction]       =   !empty($path)?array_pop($path):ACTION_NAME;
			$var[$varController]   =   !empty($path)?array_pop($path):CONTROLLER_NAME;
			if($maps = C('URL_ACTION_MAP')) {
				if(isset($maps[strtolower($var[$varController])])) {
					$maps    =   $maps[strtolower($var[$varController])];
					if($action = array_search(strtolower($var[$varAction]),$maps)){
						$var[$varAction] = $action;
					}
				}
			}
			if($maps = C('URL_CONTROLLER_MAP')) {
				if($controller = array_search(strtolower($var[$varController]),$maps)){
					$var[$varController] = $controller;
				}
			}
			if($urlCase) {
				$var[$varController]   =   parse_name($var[$varController]);
			}
			$module =   '';

			if(!empty($path)) {
				$var[$varModule]    =   array_pop($path);
			}else{
				if(C('MULTI_MODULE')) {
					if(MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')){
						$var[$varModule]=   MODULE_NAME;
					}
				}
			}
			if($maps = C('URL_MODULE_MAP')) {
				if($_module = array_search(strtolower($var[$varModule]),$maps)){
					$var[$varModule] = $_module;
				}
			}
			if(isset($var[$varModule])){
				$module =   $var[$varModule];
				unset($var[$varModule]);
			}

		}
	}

	if(C('URL_MODEL') == 0) { // 普通模式URL转换
		$url        =   __APP__.'?'.C('VAR_MODULE')."={$module}&".http_build_query(array_reverse($var));
		if($urlCase){
			$url    =   strtolower($url);
		}
		if(!empty($vars)) {
			$vars   =   http_build_query($vars);
			$url   .=   '&'.$vars;
		}
	}else{ // PATHINFO模式或者兼容URL模式
		if(isset($route)) {
			$url    =   __APP__.'/'.rtrim($url,$depr);
		}else{
			$module =   defined('BIND_MODULE') ? '' : $module;
			$url    =   __APP__.'/'.($module?$module.MODULE_PATHINFO_DEPR:'').implode($depr,array_reverse($var));
		}
		if($urlCase){
			$url    =   strtolower($url);
		}
		if(!empty($vars)) { // 添加参数
			foreach ($vars as $var => $val){
				if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
			}
		}
		if($suffix) {
			$suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
			if($pos = strpos($suffix, '|')){
				$suffix = substr($suffix, 0, $pos);
			}
			if($suffix && '/' != substr($url,-1)){
				$url  .=  '.'.ltrim($suffix,'.');
			}
		}
	}
	if(isset($anchor)){
		$url  .= '#'.$anchor;
	}
	if($domain) {
		$url   =  (is_ssl()?'https://':'http://').$domain.$url;
	}
	return '/index.php'.$url;
}


/**
 * [keditor 编辑器]
 * @param   $param['name'] 字段名称英文 
 * @param   $param['content'] 字段值
 * @param   $param['style'] 编辑器模式（默认完整  2简单模式）
 * @return string        
 */
function keditor($param)
{
	$name = $param['name'];
	$content = $param['content'];
	$style = isset($param['style'])?$param['style']:1;
	$str='';
	$str .= "<script type='text/javascript' src='".__BASE__."static/Keditor/kindeditor-all-min.js'></script>";
	$uploadScript = UC('Admin/Upload/keditor_upload');

	$toolbar = '';
	if($style==2)
	{

		$toolbar = '
            items :["source","code","fullscreen","|","forecolor", "bold", "italic", "underline",
        "removeformat", "|", "justifyleft", "justifycenter", "justifyright", "insertorderedlist",
        "insertunorderedlist", "|", "emoticons", "link"],';

	}


	$str .=<<<php
        <script type="text/javascript">
        var optionVar ='{$name}';
        KindEditor.ready(function(K) {
                var optionVar= "editor"+optionVar;
                optionVar = K.create('#{$name}', {
                    //cssPath : '../plugins/code/prettify.css',
                    //uploadJson : '__BASE__/static/Keditor/php/upload_json.php',
                    uploadJson : '{$uploadScript}',
                    fileManagerJson : '__BASE__/static/Keditor/php/file_manager_json.php',
                    {$toolbar}
                    width:'99%',
                    height:'300px',
                    allowFileManager : true,
                    afterCreate : function() {
                        var self = this;
                        K.ctrl(document, 13, function() {
                            self.sync();
                            K('form[name=example]')[0].submit();
                        });
                        K.ctrl(self.edit.doc, 13, function() {
                            self.sync();
                            K('form[name=example]')[0].submit();
                        });
                    },
                    //langType:'en',
                    afterBlur: function(){this.sync();}
            });
        });
        </script>
        <textarea name="{$name}" id="{$name}" >{$content}</textarea>
php;
                    return $str;

}

/**
 * 判断目录是否存在 创建目录
 * @param unknown $dir
 * @param number $mode
 * @return boolean
 */
function check_dir($dir, $mode = 0777)
{
	if (is_dir($dir) || @mkdir($dir, $mode)){
		return true;
	}
	if (!(check_dir(dirname($dir), $mode))){
		return false;
	}
	return @mkdir($dir, $mode);
}

/**
 * html5单文件上传
 * @param   $param['name'] 字段名称英文
 * @param   $param['content'] 字段值
 * @param   $param['table'] 删除时回调操作的表
 * @param   $param['p_key'] 删除时回调操作的主键
 * @param   $param['field_name'] 删除时回调操作的主键字段名   
 * @return string
 */
function unifile_upload($param)
{
	$name = $param['name'];
	$content = $param['content']?$param['content']:'';
	$table = $param['table']?$param['table']:'';
	$p_key = $param['p_key']?$param['p_key']:'';
	$field_name = $param['field_name']?$param['field_name']:'';
	$module = $param['module']?$param['module']:'other';
	$up_url = UC('Admin/Upload/img_upload');
	$up_del = UC('Admin/Upload/del');
	$str='';
	$str .= "<script type='text/javascript' src='".__BASE__."Public/static/admin/js/zxxFile_1.0.js?=1'></script>";
	$str .= "<script type='text/javascript' src='".__BASE__."Public/static/admin/js/mobileBUGFix.mini.js'></script>";
	$str .= '<div class="zxx_img" id="addPic">';
	if($content){
		$str .= '<img src="'.__BASE__.'Public/'.$content.'" onerror="this.src=';
	}else{
		$str .= '<img src="'.__BASE__.'Public/static/admin/img/default.gif" onerror="this.src=';
	}
	$str .= "'".__BASE__."Public/static/admin/img/default.gif'\"";
	$str .= 'height="80" width="80" />';	
	$str .= '<input type="file" id="fileImage" name="fileselect[]" multiple style="z-index:200;opacity:0;filter:alpha(opacity=0);-ms-filter:alpha(opacity=0); position:absolute; top:0; left:0; width:60px; height:60px;">';
	if(!$content){
		$str .= '<a href="javascript:;" class="cBtn spr db upload_delete" style="display:none;" data-index="2">关闭</a>';
	}else{
		$str .= '<a href="javascript:;" class="cBtn spr db upload_delete" data-index="2">关闭</a>';
	}
	$str .= '<input type="hidden" name="'.$name.'" value="'.$content.'" />
			 <input type="hidden" name="table" value="'.$table.'" />
			 <input type="hidden" name="p_key" value="'.$p_key.'" />
			 <input type="hidden" name="field_name" value="'.$field_name.'" />
          </div>';
	$str .=<<<php
	    <script type="text/javascript">
		var filelength = 0;
		var z_params = {
		resize: 1,                          //上传压缩比率
		module:'$module',
		fileInput: $("#fileImage").get(0),
		dragDrop: $("#fileDragArea").get(0),
		upButton: $("#fileSubmit").get(0),
		url: "{$up_url}",
		filter: function(files) {
			var arrFiles = [];
			for (var i = 0, file; file = files[i]; i++) {
				 if (file.size >= 5120000) {
					 alert('您这个"'+ file.name +'"文件大小过大，应小于5M');
				 } else {
					arrFiles.push(file);	
				 }
			}
			return arrFiles;
		},
		onSelect: function(files,length) {
			var l=length-files.length,i=0,html='';
			var funAppendImage = function() {
			       	file=files[i];
			       	if (file) {
			       		var reader = new FileReader();
						reader.onload = function(e) {
				       		 $("#addPic img").attr("src",e.target.result);
					       	 $("#addPic a").show();
								i++;
							funAppendImage();
						}
						reader.readAsDataURL(file);
			       	} else {
			       		
			    	}  	
	 		};
	 		funAppendImage();		
		},
		onSuccess: function(file, response) {
			$('input[name=$name]').val(response);
		},
		onFailure: function(file) {
			alert(file);
		}
	};
	$("#addPic a").click(function(){
		var svalue = $('input[name=$name]').val();
		var id = $('input[name=id]').val();
		if(id != '' || id != null){
			var table = $('input[name=table]').val();
			var p_key = $('input[name=p_key]').val();
			var field_name = $('input[name=field_name]').val();
			var data = {"src":svalue,"id":id, "table":table, "p_key":p_key, "field_name":field_name};
		}else{
			var data = {"src":svalue};
		}
		$.post("{$up_del}", data, 
				  function(res){
					alert(res.info);
		            if (res.status == 1) {
		            	$('input[name=$name]').val('');
		            	$("#addPic img").attr("src",'__BASE__/static/admin/img/default.gif');
				       	$("#addPic a").hide();
		            } 
		        },'json');
	})
	if (window.File && window.FileList && window.FileReader && window.Blob) {
		   ZXXFILE = $.extend(ZXXFILE, z_params); 
	       ZXXFILE.init();  
	} else {
	    info.innerHTML = "您的浏览器不支持HTML5长传";
	   info.className="tips";
	}
</script>
php;
                    return $str;

}

/**
 * 上传多张图片
 * @param  $param['name']       数据库字段名称英文
 * @param  $param['content']    字段值
 * @return string
 */
function multi_upload_pic($param)
{
	$name = $param['name'];
	$content = $param['content'];

	$id = $param['id']?$param['id']:'';
	$del = UC('Admin/Upload/del_one_img');
	$edit_del = UC('Admin/Upload/del_exist_img');
	$str = '';

	$str .= "<script type='text/javascript' src='".__BASE__."Public/static/Keditor/kindeditor.js'></script>";
	$str .= "<script type='text/javascript' src='".__BASE__."Public/static/Keditor/lang/zh_CN.js'></script>";
	$str .= "<link rel='stylesheet' href='".__BASE__."Public/static/Keditor/themes/default/default.css'></script>";
	
	$str .= '<div class="col-sm-3" id="image" >
				<p id="addPic">
				<input type="hidden" name="'.$name.'" id="upload_url" value="'.$content.'" />
				<input type="button" id="uploadimage" value="选择图片" />
				</p>'
			;
				
	if($param['content']){
		foreach(explode(';',$param['content']) as $k=>$v){
			$k += 1;	
			$str .= '<div style="width:500px;" class="fli" id="del'.$k.'">
			<img src="'.__BASE__.'Public/'.$v.'" class="picture" style="width:80px;height:70px;float:left;" />
			<input type="button" class="button" title="" style="margin-top:80px;margin-left:-60px;" onclick="del('.$k.','.'\''.$v.'\''.');" value="删除"  />
			<div class="blank"></div>
			</div>';			
		}
	}
	
	$str .= '</div>';
		
	$str .=<<<php
		<script type="text/javascript">
		    var i = 1;
			KindEditor.ready(function(K) {
				var editor = K.editor({
					allowFileManager : true
				});
				K('#uploadimage').click(function() {
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							showRemote : false,							
							imageUrl : K('#uploadurl').val(),
							clickFn : function(url, title, width, height, border, align) {
								//截取相对路径
								url = url.substring(url.indexOf('/data'));		
								$("#upload_url").val($("#upload_url").val() + url + ';');
								html = '<li id="uploadList_'+ i+'" class="fli" style="list-style: none;">'+
								 '<div class="photoCut" style="width:500px;">'+
								 '<img src="'+url+'" class="attchImg" style="width:80px;height:70px;float:left;" alt="photo">'+			                  
					             '<input type="button" class="cBtn spr db upload_delete" title="" data-index="'+ i +'" style="margin-top:80px;margin-left:-60px;" onclick="dell('+ i +');" value="删除"  /></div>'+
					             '</li>';					
						       	$('#image').append(html);
								editor.hideDialog();
								i++;
							}
						});
					});
				});
			});
			
			//删除图片
			function dell(i){
				var src = $('#uploadList_'+i).children('.photoCut').children('img').attr('src');
				var img_url = $("#upload_url").val();
				ifexist = img_url.indexOf(src+';');
				if(ifexist != -1){
					imgUrl = img_url.replace(src+';','');
				}else{
					imgUrl = img_url.replace(src,'');
				}
				$("#upload_url").val(imgUrl);
				data = {
						'src':src
					   }
				$.post("{$del}", data, 
				    function(data){
						alert(data.info);
			            if (data.status == 1) {			                
							$('#uploadList_'+i).remove();
			        	} 
				},'json'); 
			}
						
		    //删除已存在的图片
			function del(delName,delId){
				var img_url = $("#upload_url").val();
				ifexist = img_url.indexOf(delId+';');
				if(ifexist != -1){
					imgUrl = img_url.replace(delId+';','');
				}else{
					imgUrl = img_url.replace(delId,'');
				}
				$("#upload_url").val(imgUrl);
				data = {
						'url':delId
					   }	
				$.post("{$edit_del}", data, 
				    function(data){
						alert(data.info);
			            if (data.status == 1) {	
							
							$('#del'+delName).remove();		                							
			        	} 
				},'json');	
														
			}
			
		</script>
	
php;
	return $str;

}

/**
 * 
 * @param unknown $param
 * @param   $param['field_name'] 搜索回调操作的字段名
 * @param   $param['field_title'] 搜索标题
 * @return string
 */
function cjax_table_top($param){
	$str = '<div class="row">
				<div class="col-sm-6">
					<div id="sample-table-2_length" class="dataTables_length">
					<label>显示
					<select size="1" id="pagesize" aria-controls="sample-table-2" onchange="cengp_Page(1)">
						<option value="10" selected="selected">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select> 
					条数据
					</label>
					</div>
				</div>';
		if($param['field_name']){
			$str .= '<div class="col-sm-6">
						<div class="dataTables_filter" id="sample-table-2_filter">
							<label>搜索: <input type="text" name="'.$param['field_name'].'" placeholder="'.$param['field_title'].'" aria-controls="sample-table-2">&nbsp;&nbsp;
								<a href="javascript:cengp_Page(1);" class="btn btn-xs btn-success">提交</a>
							</label>
						</div>
					</div>';
		}
		$str .= '</div>';
	return $str;
}

/**
 * @param   $param['field_name'] 搜索字段名
 * @param   $param['count'] 表单数据总数
 * @param   $param['other'] 其他需要跟着ajax回调的参数 可以是数组
 * @return string
 */
function cjax_table_bottom($param){
	$url = UC('Admin/'.CONTROLLER_NAME.'/'.ACTION_NAME);
	$field_name = $param['field_name'];
	if($field_name){
	  $js =	'title = $("input[name='.$field_name.']").val();';
	}else{
	  $js =	'title = "";';
	}
	$count = $param['count'];
	$other = $param['other'];
	$str = '';
	$str .= '<div class="row" id="pageNav"></div>';
	$str .=<<<php
	    <script type="text/javascript">
		$(function(){
			cengp_Page(1, 1);
			$("input[name='{$field_name}']").focus();
			$(document).keydown(function(e){
				if(e.keyCode == 13) {
					e.preventDefault(); 
					cengp_Page(1);
				}
			});
		})
		function cengp_Page(curPage, innt){ 
			pagesize = $('#pagesize option:selected').val();
		    if(innt != 1){
		    	$js
		    	if(title){
					data = {"page":curPage,"pagesize":pagesize,"$field_name":title,$other};
			    }else{
			    	data = {"page":curPage,"pagesize":pagesize,$other};
			    }
			    $.post("$url", data, 
					  function(data){
						$("#list").html(data.info);
						supage('pageNav','cengp_Page','',curPage, data.url, pagesize); 
			        },'json');
		    }else{
		    	supage('pageNav','cengp_Page','',curPage, $count, pagesize);  
		    }
		}  
		</script>
php;
	return $str;
}



