//搜索
function search(){
	var kw = $('#kw').val();
	if(kw==''){
		alert('搜索关键字不能为空');
		return;
	}
	window.location.href = URL+'product/product/list?kw='+kw;
}