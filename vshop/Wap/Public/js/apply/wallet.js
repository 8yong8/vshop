/* 转账 */
//确认
function confirm(){
	var account = $('#account').val();
	var qraccount = $('#qraccount').val();
	if(account==''){
	  showMessage('帐号必须',2000);
	  return;
	}
	if(account!=qraccount){
	  showMessage('帐号必须一致',2000);
	  return;
	}
	art.dialog({
		ok: function () {
			confirm_pay();
			//alert($('#password').val());
		},
		content: '输入支付密码：<input type="password" name="pwd" id="pwd" style="border:1px black solid;height:25px;">'
	});
	return;
}

//确认支付
function confirm_pay(){
	var pwd = $('#pwd').val();
	$('#password').val(pwd);
	send('transfer',URL+'user/wallet/transfer');
}