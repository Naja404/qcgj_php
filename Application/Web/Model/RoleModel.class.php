<?php
/**
 * 角色权限模型
 */
namespace Web\Model;
use Think\Model;

class RoleModel extends Model{

	protected $ruleValidate = array(
			array('module', 'require', '{%ERR_RULE_MODULE}', 1),
			array('moduel_title', 'require', '{%ERR_RULE_MODULE_TITLE}', 1),
			array('action_title', 'require', '{%ERR_RULE_ACTION_TITLE}', 1, 'unique', 1),
			array('action_url', 'require', '{%ERR_RULE_ACTION_URL}', 1, 'unique', 1),
			array('sort', 'number', '{%ERR_RULE_SORT}', 1),
			array('type', array(1, 2), '{%ERR_RULE_TYPE}', 1, 'in'),
		);

	public function addRule($ruleData = array()){
		$this->options['validate'] = $this->ruleValidate;
		$res = $this->validate($this->ruleValidate)->create($ruleData);

		
		if ($this->create($ruleData)) {

		}
		$ruleID = $this->table('qcgj_role_rule')->add($ruleData);

		return $ruleID;
	}
}
