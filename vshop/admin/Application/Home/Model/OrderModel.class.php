<?php 
namespace Home\Model;
use Think\Model;
class OrderModel extends CommonModel {
	//protected $error_code = 0;
	protected $error_msg = ''; //错误信息

	// 定义自动写入和更新的时间戳字段
	protected $autoCreateTimestamps = array('create_time');
	protected $autoUpdateTimestamps = array('update_time');
	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
		array('cid','require','分别必须！'),
		//array('email','email','邮箱格式错误！',2),
		//array('content','require','内容必须'),
		//array('title','','标题已经存在',0,'unique','add'),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
	);


	 /**
     +----------------------------------------------------------
     * 确认订单
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function confirm_order($vo){
	  if(($vo['payment_mode'] == 2 || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 1)) && $vo['status'] == 0){
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 确认付款
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function payment($vo){
	  if((($vo['payment_mode'] == 2 && $vo['delivery_status'] == 1 && $vo['pay_status'] == 0) || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 0 && $vo['delivery_status'] == 0)) && $vo['status'] == 1){
		/*
		确认订单：[线下支付 && 未发货 && 未支付 ] || [线下支付 && 未支付 && 未发货] && 订单已确认
		*/
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 物流信息修改
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function deliver($vo){
	  if(($vo['payment_mode'] == 2 || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 1)) && $vo['status'] == 1 && $vo['delivery_status'] == 0 ){
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 确认收货
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function receipt($vo){
	  if(($vo['payment_mode'] == 2 || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 1)) && $vo['status'] == 1 && $vo['delivery_status'] == 1 ){
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 申请退款
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function apply($vo){
	  if(($vo['status'] == 1 || $vo['status'] == 2) && $vo['refund_status']==0){
		/*
		确认订单：[已支付 || 已完成交易 || 先发货后支付] && 未申请退款
		*/
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 未发货同意退款
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function nd_agree($vo){
	  if($vo['refund_status']==1 && $vo['delivery_status']==0){
		/*
		同意退款：[已申请退款 && 未发货] 
		*/
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 已发货同意退款
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function yd_agree($vo){
	  if($vo['refund_status']==1 && $vo['delivery_status']>0 && ($vo['status']==1 || $vo['status']==2)){
		/*
		同意退款：[已申请退款 && 已发货 && [已支付或完成交易] ] 
		*/
	    return true;
	  }else{
		if($vo['status']==0){
		  $this->error_msg = '未支付';
		}else{
		  $this->error_msg = '订单状态错误';
		}
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 确认退货
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function return_goods($vo){
	  if($vo['refund_status']==3){
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

	 /**
     +----------------------------------------------------------
     * 确认退款
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $vo 数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function return_refund($vo){
	  if($vo['refund_status']==4){
	    return true;
	  }else{
		$this->error_msg = '订单状态错误';
	    return false;
	  }
	}

}
?>