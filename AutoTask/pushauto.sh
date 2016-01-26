#!/bin/sh
#调用推送监测controller
wget -q --spider http://molo.ichurun.com/index.php/Wechat/Notify/pushNotify.html

#  */5 * * * * /usr/bin/curl -o /var/www/html/ticket/notify.log http://molo.ichurun.com/index.php/Wechat/Notify/pushNotify.html