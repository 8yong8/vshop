$(document).ready(function(){
  $('#show_msg').click(function(){
	 sentmsm();
  });

  $('#reg_do').click(function(){
	  var agreement = $("#agreement").is(':checked');
	  if(agreement){
		send('reg',URL+'user/account/register');
	  }else{
		art.dialog({
			time: 3,
			content: '必须同意美鞋家服务协议才可注册'
		});
	  }
  });

});