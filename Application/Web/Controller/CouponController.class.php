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
	 * ajax获取内容
	 *
	 */
	public function ajaxCouponList(){
		echo '{"draw":2,"recordsTotal":57,"recordsFiltered":57,"data":[["Charde","Marshall","Regional Director","San Francisco","2008\/10\/16",470600],["Colleen","Hurst","Javascript Developer","San Francisco","2009\/09\/15",205500],["Dai","Rios","Personnel Lead","Edinburgh","2012\/09\/26",217500],["Donna","Snider","Customer Support","New York","2011\/01\/25",112000],["Doris","Wilder","Sales Assistant","Sidney","2010\/09\/20",85600],["Finn","Camacho","Support Engineer","San Francisco","2009\/07\/07",87500],["Fiona","Green","Chief Operating Officer (COO)","San Francisco","2010\/03\/11",850000],["Garrett","Winters","Accountant","Tokyo","2011\/07\/25",170750],["Gavin","Joyce","Developer","Edinburgh","2010\/12\/22",92575],["Gavin","Cortez","Team Leader","San Francisco","2008\/10\/26",235500]]}';exit;
		$data = $this->couponModel->getCoupon();

		$this->ajaxReturn($data);
	}

	/**
	 * 变量定义
	 */
	private function _assignText(){
		$this->assign('pageTitle', 'listview');
	}
}
