<?php
/**
 * 用户模块
 */

namespace Api\Controller;
use Think\Controller;

class UserController extends ApiController {
    
    // 用户模型
    public $userModel;

    public function _initialize(){  

        parent::_initialize();
        $this->userModel = D('User');
    }

    /**
     * 注册用户
     * @param int $regSource 用户来源 1.iOS 2.android
     * @param string $password 密码 sha1加密
     * @param string $mobile 手机号
     * @param string $validateCode 验证码
     * @param string $inviteCode 邀请码
     */
    public function reg(){
    	
    	$data = array(
				'regSource'    => (int)I('get.regSource'),
				'password'     => sha1(I('get.password')),
				'mobile'       => is_numeric(I('get.mobile')),
				'validateCode' => I('get.validateCode'),
				'inviteCode'   => I('get.inviteCode'),
    		);

    	$checkStatus = $this->checkRegData($data);

    	if (is_array($checkStatus)) {
    		$this->ajaxReturn($checkStatus, 'JSON');
    	}

    	$regStatus = $this->userModel->registerUser($data);

    }

    /**
     * 验证用户注册
     * @param array $data 注册信息内容
     */
    public function checkRegData($data = array()){

    	$mobileStatus = $this->userModel->checkMobile($data['mobile']);
    	// 检测手机号
    	if (is_array($mobileStatus)) {
    		return $mobileStatus;
    	}


    }

}