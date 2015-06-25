<?php
/**
 * 角色权限模块
 */
namespace Web\Controller;
use Think\Controller;

class RoleController extends WebController {

	public $roleModel;

	public function _initialize(){
		$this->roleModel = D('Role');
		$this->_assignText();
	}

	public function index(){

	}

	/**
	 * 角色列表
	 */
	public function rolelist(){
		$this->display();
	}

	/**
	 * 角色添加
	 */
	public function addrole(){

		if (!IS_AJAX) {
			$this->display();
			exit;
		}
	}

	/**
	 * 权限添加
	 */
	public function addrule(){

		// if (!IS_AJAX) {
		// 	$this->ajaxReturn($this->ajaxRes);
		// }

		$ruleData = I('post.');
		$ruleData = array(
				'module' => 'Coupon',
				'module_title' =>'优惠券管理',
			);

		$result = $this->roleModel->addRule($ruleData);

		if ($result === true) {
			$this->ajaxRes = array(
					'status' => 0,
					'msg'    => L('TEXT_RULE_ADD_SUCCESS'),
				);
		}else{
			$this->ajaxRes['msg'] = $result;
		}

		$this->ajaxReturn($this->ajaxRes);
	}

	/**
	 * 角色编辑
	 */
	public function editrole(){

	}

	/**
	 * 用户列表
	 */
	public function userlist(){

	}

	/**
	 * 变量定义
	 */
	private function _assignText(){
		$this->assign('pageTitle', L('TEXT_ROLE_TITLE_'.ACTION_NAME));
	}
}
