<?php

namespace Api\Controller;
use Think\Controller;

class ApiController extends Controller {

	// 用户id
	protected $userID;

	// 会话id
	protected $sessionID;

	// api 返回基础内容
	public $apiResponse;

	/**
	 * 权限验证
	 * @param bool $hasVerify 是否需要验证
	 */
	protected function _initialize($hasVerify = true){

		$this->apiResponse();

		$this->checkSessionStatus();

		if (!$hasVerify) {

			return;
		}

		$this->checkAuth();
	}

	/**
	 * api基本返回内容
	 *
	 */
	public function apiResponse(){
		$this->apiResponse = array(
				'errcode' => '0',
				'errmsg' => '',
				'datas' => NULL,
			);
	}

	/**
	 * 检测会话id
	 *
	 */
	public function checkSessionStatus(){
		// $this->sessionID = I('');
	}

	/**
	 * 检测用户
	 *
	 */
	public function  checkAuth(){

	}
}
