@CLS
@SET VERCONF=137_mysql.conf
@IF NOT "%1" == "" @SET VERCONF=%1
@SET path=.;bin;%path%
@indexer  -c etc\%VERCONF% article_index
@D:\wamp\bin\mysql\mysql5.5.16\bin/mysql.exe mysql -u137137home -psdZbf3o1 <D:\wamp\www\gongsi\obj1\interface\coreseek/update.sql
@searchd  -c etc\%VERCONF% 
@%ComSpec% /Q /K ECHO.