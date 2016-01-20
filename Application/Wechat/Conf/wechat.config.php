<?php


return array(
	
	/* 微信公众号配置 */
	'WECHAT_TOKEN' 			=> 'reraweraedfsdgdfgf1233211354344',
	'WECHAT_APPID' 			=> 'wx55cfd637e5ee245b',
	'WECHAT_APPSECRET' 	    => 'bc5e446cd89da9d8eb5be44effe9d41e', 
	'WECHAT_ENCODINGAESKEY' => 'mwZcAgJFmqdjExG7Dny9oOaaFOsUTwdEaHQVOJ6SOJ6',
	'WECHAT_WELCOME' 		=> '欢迎关注微信公众号！功能正在开发中，请耐心等待哦！期待你的加入哦~',
    
    /*** 支付配置  **/
    
    'WECHAT_MCH_ID'         => '1282838201',   //微信商户id
    'WECHAT_PAY_KEY'        => '8F24AE8E4975A8E8A370EF2505E6332C',  //微信支付key

    'WECHAT_MENU'           => array(
        'button' => array(
            array('type'=>'view','name'=>'魔幻城','url'=>__BASE__."index.php/Wechat/Ticket/index"),
            array('type'=>'view','name'=>'推荐有礼','url'=>__BASE__."index.php/Wechat/Coupon/index"),
            array('type'=>'view','name'=>'个人中心','url'=>__BASE__."index.php/Wechat/User/index"),
        )
    ),
	
);
