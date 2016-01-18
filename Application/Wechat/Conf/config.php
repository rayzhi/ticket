<?php

return array(
	
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'chat_', // 缓存前缀
    'DATA_CACHE_TYPE'   => 'File', // 数据缓存类型
    
	'ACTION_SUFFIX'  => 'Act', // 控制器方法后缀
	'DEFAULT_C_LAYER' => 'Controller',  // 默认的控制器层
	'APP_FILE_CASE'  => true, // 检查文件大小写  
	
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'ticket_wechat', //session前缀
    'COOKIE_PREFIX'  => 'ticket_wechat', // Cookie前缀 避免冲突
    
    /* 自定义参数 */
    'SITE_TITLE' => '药师帮',
    'TITLE' => '药师帮管理系统',
    'SITE_COMPANY' => '广州速道网络科技有限公司',
    
	'LOAD_EXT_CONFIG'=>'wechat.config',
);
