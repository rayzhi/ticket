#!/bin/sh
#调用推送监测controller
wget -q --spider http://ticket.ichurun.com/index.php/Wechat/Notify/pushNotify.html

#  */5 * * * * /usr/bin/curl -o /var/www/html/ticket/notify.log http://ticket.ichurun.com/index.php/Wechat/Notify/pushNotify.html