<?php
namespace Home\Controller;
use Think\Controller;
class WapController extends CommonController {

    public function _initialize() {
	    parent::_initialize();
	    $this->db = D('Position_data');
    }

    /**
     * WAP首页缓存
     */
     function SetIndex(){
        $pos_config = array(
            0 =>array
            (
                'id'=>10,
                'limit'=>6,
                'style'=>'mgt15',
            ),
            1 =>array
            (
                'id'=>11,
                'limit'=>3,
                'style'=>'bbb',
            ),
            2 =>array
            (
                'id'=>12,
                'limit'=>1,
                'style'=>'mgt15',
            ),	
            3 =>array
            (
                'id'=>13,
                'limit'=>3,
                'style'=>'bbb',
            ),	
            4 =>array
            (
                'id'=>14,
                'limit'=>1,
                'style'=>'mgt15',
            ),	
            5 =>array
            (
                'id'=>15,
                'limit'=>3,
                'style'=>'bbb',
            ),
            6 =>array
            (
                'id'=>16,
                'limit'=>3,
            ),
            7 =>array
            (
                'id'=>17,
                'limit'=>20,
            ),
            
        );
         $positions = $this->get_position_data($pos_config);
          if($_POST['status'])$msg['data'] = $positions;
          $return = $this->SetCache('index',$positions);
          if($return){
            $msg['error_code'] = 0;
          }else{
            $msg['error_code'] = 8002;
          }
          echo json_encode($msg);exit;
     }

  //获取位置数据
  protected function get_position_data($pos_config){
	$model = M('Position_data');
	foreach($pos_config as $position){
		$wdata['position_id'] = $position['id'];
		$wdata['status'] = 1;
		$positionsData = $model->where($wdata)->order('sort asc,id asc')->limit($position['limit'])->select();
		foreach ($positionsData as $key=>$positionData) 
		{
			$params = unserialize($positionData['params']);
			if($positionData['url']!=''){
				$positionsData[$key]['url'] = $positionData['url'];
			}else if($positionData['data_type']=='product_detail'){
				$positionsData[$key]['url'] = __APP__.'/Product/detail/?id='.$params['product_id'];
			}else if($positionData['data_type']=='product_list'){
				$param_str = http_build_query($params);
				$positionsData[$key]['url'] = __APP__.'/Product/list?'.$param_str;				
			}else if($positionData['data_type']=='article_detail'){
				$positionsData[$key]['url'] = __APP__.'/News/detail/?id='.$params['news_id'];					
			}
		}
		$id = $position['id'];
		$data[$id] = $positionsData;
	}
	return $data;  
  }


}
