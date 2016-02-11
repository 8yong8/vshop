#!/bin/sh
kill `cat /home/wwwroot/gongsi/obj1/var/log/137home.pid`
/usr/local/coreseek/bin/indexer -c /home/wwwroot/gongsi/obj1/interface/coreseek/etc/137_mysql_l.conf article_index >> /var/log/coreseek/mainindexlog
/usr/local/coreseek/bin/searchd -c /home/wwwroot/gongsi/obj1/interface/coreseek/etc/137_mysql_l.conf