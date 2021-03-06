<?php
/**
 * 优惠券模块
 */
namespace Web\Controller;
use Think\Controller;

class CouponController extends WebController {

	public $couponModel;

	public function _initialize(){
		$this->couponModel = D('Coupon');
		$this->_assignText();
	}

	public function index(){
		$this->listview();
	}

	/**
	 * 优惠券列表
	 */
	public function listview(){
		$this->display();
	}

	/**
	 * 优惠券报表
	 *
	 */
	public function analysis(){
		$this->display();
	}

	/**
	 * ajax获取内容
	 *
	 */
	public function ajaxCouponList(){

		$pageLength = I('get.length');
		$pageStart  = (I('get.start') / $pageLength) + 1;
		$search     = I('get.search');

		$data = $this->couponModel->getCouponList($pageStart, $pageLength, $search['value'], 'DT');

		$this->ajaxReturn($data);
	}

	/**
	 * 变量定义
	 */
	private function _assignText(){
		$this->assign('pageTitle', L('TEXT_COUPON_TITLE_'.ACTION_NAME));
	}
}
