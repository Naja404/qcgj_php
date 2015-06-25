<?php
/**
 * 角色权限模型
 */
namespace Web\Model;
use Think\Model;

class RoleModel extends Model{

	// array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
	// protected $ruleValidate = array(
	// 		array('module', 'require', '{%ERR_RULE_MODULE}', 1),
	// 		array('moduel_title', 'require', '{%ERR_RULE_MODULE_TITLE}', 1),
	// 		array('action_title', 'require', '{%ERR_RULE_ACTION_TITLE}', 1, 'unique', 1),
	// 		array('action_url', 'require', '{%ERR_RULE_ACTION_URL}', 1, 'unique', 1),
	// 		array('sort', 'number', '{%ERR_RULE_SORT}', 1),
	// 		array('type', array(1, 2), '{%ERR_RULE_TYPE}', 1, 'in'),
	// 	);

	/**
	 * 添加权限内容
	 * @param array $ruleData 规则数据内容
	 */
	public function addRule($ruleDatas = array()){

		// $this->tableName = 'qcgj_role_rule';

		// $validateRes = $this->validate($this->ruleValidate)->create($ruleData);

		// if ($validateRes != true) {
		// 	return $validateRes;
		// }

		$ruleDatas['created_time'] = NOW_TIME;
		$ruleDatas['status'] = 1;

		// $sql = "insert into qcgj_role_rule set module = 'Coupon', module_title = '优惠券管理'";
		// $this->query($sql);

		$addRes = $this->table('qcgj_role_rule')->add($ruleDatas);
		return $addRes >= 1 ? true : L('ERR_PARAM');
	}
}
