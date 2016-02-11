<?php 
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
defined('IN_APP') or exit('Access Denied');
class syscache
{	
	/* 商品分类 */
	public function goods_category() {
		return model('Category')->build_cache();
	}

	/* 支付方式 */
	public function site_payment() {
		return model('payment')->build_cache();
	}

	public function site_delivery() {
		return model('delivery')->build_cache();
	}

	/* 站点设置 */
	public function site_config() {
		return TRUE;
	}

	/* 模板缓存 */
	public function cache_template() {
		libfile('Dir');
		$dir = new Dir();
		return $dir->del(CACHE_PATH);
	}

	/* 字段缓存 */
	public function cache_field() {
		libfile('Dir');
		$dir = new Dir();
		return $dir->del(DATA_PATH.'_fields');
	}

	/* 系统垃圾 */
	public function cache_tmp() {
		libfile('Dir');
		$dir = new Dir();
		return $dir->del(TEMP_PATH);		
	}

	/* 错误日志 */
	public function cache_log() {
		libfile('Dir');
		$dir = new Dir();
		return $dir->del(LOG_PATH);
	}
	
	/*地区信息*/
	public function site_region() {
		return model('region')->build_cache();
	}
}