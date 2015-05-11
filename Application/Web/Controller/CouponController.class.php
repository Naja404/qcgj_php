<?php
/**
 * 首页模块
 */
namespace Web\Controller;
use Think\Controller;

class CouponController extends WebController {

	public $couponModel;

	public function _initialize(){
		$this->couponModel = D('Coupon');
	}

	public function index(){
		$this->listview();
	}

	/**
	 * 优惠券列表
	 */
	public function listview(){

		$this->assign('couponList', $this->couponModel->getCoupon());
		$this->_assignText();
		$this->display();
	}

	/**
	 * 变量定义
	 */
	private function _assignText(){
		$this->assign('pageTitle', 'listview');
	}
}
