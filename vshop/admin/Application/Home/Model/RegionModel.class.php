<?php
namespace Home\Model;
use Think\Model;
class RegionModel extends CommonModel {
    //自动完成
    protected $_auto = array(
       
    );
    //自动验证
    protected $_validate = array(
        array('id','require','ID必须！',1,'',2), 
        array('area_name','require','地域名称不能为空！',1,'',3), // 在新增的时候验证name字段是否唯一
    );
	public function build_cache() {
		$sqlmap = array();
		$rows = $this->where($sqlmap)->select();
		$result = array();
		if($rows) {
			foreach($rows as $row) {
				$result[$row['id']] = $row['class_name'];
			}
		}
	}
    /**
     * 地区列表
     */
    public function lists($parent_id = 0, $order = 'sort asc,id asc', $field = true) {
        $map = array('pid' => $parent_id);
        return $this->field($field)->where($map)->order($order)->select();
    }
   

    

}
