<?php
/**
 * 商户模块
 */
namespace Web\Controller;
use Think\Controller;

class ShopController extends WebController {

	public $ShopModel;

	public function _initialize(){
		$this->ShopModel = D('Shop');
		$this->_assignText();
	}

	public function index(){
		$this->listview();
	}

	/**
	 * 商户列表
	 */
	public function listview(){
		$this->display();
	}

	/**
	 * ajax获取内容
	 *
	 */
	public function ajaxShopList(){

		$pageLength = I('get.length');
		$pageStart  = (I('get.start') / $pageLength) + 1;
		$search     = I('get.search');

		$data = $this->ShopModel->getShopList($pageStart, $pageLength, $search['value'], 'DT');

		$this->ajaxReturn($data);
	}

	/**
	 * 变量定义
	 */
	private function _assignText(){
		$this->assign('pageTitle', L('TEXT_SHOP_TITLE_'.ACTION_NAME));
	}
}
