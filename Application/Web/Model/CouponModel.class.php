<?php
/**
 * 优惠券模型
 */
namespace Web\Model;
use Think\Model;

class CouponModel extends Model{

	/**
	 * 获取优惠券列表
	 */
	public function getCoupon(){
		return $this->table(tname('coupon'))->where()->select();
	}
}
