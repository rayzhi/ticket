<?php


return array(
	
	/* 微信公众号配置 */
	'WECHAT_TOKEN' 			=> 'reraweraedfsdgdfgf1233211354344',
	'WECHAT_APPID' 			=> 'wxb3cb5fc99dbe663d',
	'WECHAT_APPSECRET' 	    => 'e587e53438f778407ae22091b6cf2e56', 
	'WECHAT_ENCODINGAESKEY' => 'mwZcAgJFmqdjExG7Dny9oOaaFOsUTwdEaHQVOJ6SOJ6',
	'WECHAT_WELCOME' 		=> '欢迎关注微信公众号！功能正在开发中，请耐心等待哦！期待你的加入哦~',
    
    /*** 支付配置  **/
    
    'WECHAT_MCH_ID'         => '1295302101',   //微信商户id
    'WECHAT_PAY_KEY'        => 'guangdongchuanqihudongmolechengf',  //微信支付key

    'WECHAT_MENU'           => array(
        'button' => array(
            array('type'=>'view','name'=>'魔幻城','url'=>__BASE__."index.php/Wechat/Ticket/index"),
            array('type'=>'view','name'=>'微信购票',
                'sub_button'=>array(
                                array('type'=>'view','name'=>'一键购票','url'=>__BASE__."index.php/Wechat/Ticket/tickeList"),
                                array('type'=>'view','name'=>'推荐有礼','url'=>__BASE__."index.php/Wechat/Recommend/index"))),
            array('type'=>'view','name'=>'个人中心','url'=>__BASE__."index.php/Wechat/User/index"),
        )
    ),
    
    //二维码存放路径
    'ERWEIMA_PATH'          => "/qrcode/",
);
