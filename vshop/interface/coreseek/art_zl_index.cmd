@CLS
@SET VERCONF=137_mysql.conf
@IF NOT "%1" == "" @SET VERCONF=%1
@SET path=.;bin;%path%
@indexer  -c etc\%VERCONF% article_zl_index --rotate
@exit;