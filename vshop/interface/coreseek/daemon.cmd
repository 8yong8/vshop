@CLS
@SET VERCONF=137_mysql.conf
@IF NOT "%1" == "" @SET VERCONF=%1
@SET path=.;bin;%path%
@searchd  -c etc\%VERCONF% 
@%ComSpec% /Q /K ECHO.