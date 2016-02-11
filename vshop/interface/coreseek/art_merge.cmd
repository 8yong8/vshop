@CLS
@SET VERCONF=137_mysql.conf
@IF NOT "%1" == "" @SET VERCONF=%1
@SET path=.;bin;%path%
@indexer  -c etc\%VERCONF% --merge article_index article_zl_index --merge-dst-range deleted 0 0 --rotate
@D:\wamp\bin\mysql\mysql5.5.16\bin/mysql.exe mysql -u137137home -psdZbf3o1 <D:\wamp\www\gongsi\obj1\interface\coreseek/update.sql
@%ComSpec% /Q /K ECHO.