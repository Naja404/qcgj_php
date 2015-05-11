<?php
/**
 * 用户模型
 */
namespace Api\Model;
use Think\Model;

class UserModel extends Model{

	/**
	 * 用户注册
	 * @param array $data 注册信息数组
	 */
	public function registerUser($data = array()){

	}

	/**
	 * 检测手机
	 * @param int $mobile 手机号
	 */
	public function checkMobile($mobile = 0){
		
		if (!checkMobileFormat($mobile)) {
			return array(
						'errcode' => '40015', 
						'errmsg'  => L('ERR_CODE_MSG_40015'),
					);
		}

		if ($this->table(tname('user'))->where(array('mobile' => $mobile))->count() > 0) {
			return array(
					'errcode' => '40015',
					'errmsg'  => L('ERR_CODE_MSG_40015'),
					);
		}

		// to do 验证 手机短信验证码
	}
}
