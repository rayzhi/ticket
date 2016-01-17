<?php

return array(
	
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'ticket_', // 缓存前缀
    'DATA_CACHE_TYPE'   => 'File', // 数据缓存类型
    
	'ACTION_SUFFIX'  => 'Act', // 控制器方法后缀
	'DEFAULT_C_LAYER' => 'Admin',  // 默认的控制器层
	'APP_FILE_CASE'  => true, // 检查文件大小写  
	
	/* 模版 */
	'TMPL_DETECT_THEME' => 	false, // 自动侦测模板主题
	'URL_HTML_SUFFIX' => '',
	'TMPL_TEMPLATE_SUFFIX' => '.tpl.php', // 模版后缀名
	
	'TMPL_PARSE_STRING'  =>array(
			'__UPLOAD__' => __BASE__.'uploads', // 增加新的上传路径替换规则
			'__STATIC__' => __BASE__.'Public/static',
			'__PATH__' => __BASE__,
			'__BASE__' => __BASE__.'admin/',
	),
	
	'LAYOUT_ON' => 1, //  启用模版布局
	'LAYOUT_NAME' => 'layout', // 布局文件名称
	'TMPL_LAYOUT_ITEM' => '{__CONTENT__}', // 布局模板的内容替换标识
	'TMPL_ACTION_ERROR' => 'Public:dispatch_jump', //默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump', //默认成功跳转对应的模板文件
    
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'ticket_admin', //session前缀
    'COOKIE_PREFIX'  => 'ticket_admin', // Cookie前缀 避免冲突
    
    /* 自定义参数 */
    'SITE_TITLE' => '售票系统',
    'TITLE' => '售票系统',
    'SITE_COMPANY' => '售票系统',
    
);
