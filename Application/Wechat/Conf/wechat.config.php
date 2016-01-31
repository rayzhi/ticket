<?php
return array(
	

    'WECHAT_WELCOME'        => '欢迎关注微信公众号！功能正在开发中，请耐心等待哦！期待你的加入哦~',

    'WECHAT_MENU'           => array(
        'button' => array(
            array('type'=>'view','name'=>'魔乐城主',
                'sub_button'=>array(
                                array('type'=>'view','name'=>'免费wifi','url'=>__BASE__."index.php/Wechat/Ticket/tickeList"),
                                array('type'=>'view','name'=>'交通指引','url'=>__BASE__."index.php/Wechat/Ticket/tickeList"),
                                array('type'=>'view','name'=>'场馆介绍','url'=>__BASE__."index.php/Wechat/Space/index"))),
            array('type'=>'view','name'=>'微信购票',
                'sub_button'=>array(
                                array('type'=>'view','name'=>'一键购票','url'=>__BASE__."index.php/Wechat/Ticket/tickeList"),
                                array('type'=>'view','name'=>'分享有礼','url'=>__BASE__."index.php/Wechat/Recommend/index"),
                                array('type'=>'view','name'=>'新春活动','url'=>__BASE__."index.php/Wechat/Activity/index?id=5"))),
            array('type'=>'view','name'=>'个人中心','url'=>__BASE__."index.php/Wechat/User/index"),
        )
    ),
	
    //二维码存放路径
    'ERWEIMA_PATH'          => "/qrcode/",
);
