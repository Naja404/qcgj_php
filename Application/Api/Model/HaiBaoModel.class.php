<?php
/**
 * 海报网模型
 */
namespace Api\Model;
use Think\Model;

class HaiBaoModel extends Model{

	protected $tableName = 'haibao_logo'; 

	/**
	 * 添加logo
	 * @param array $logo logo数组
	 */
	public function setLogo($logo = array()){
		return $this->table(tname('haibao_logo'))->add($logo);
	}

	/**
	 * 获取logo数据列表
	 *
	 */
	public function getLogoList(){
		return $this->select();
	}

	/**
	 * 更新logo路径
	 * @param int $logoId 
	 * @param string $path
	 */
	public function upLogoPath($id = 0, $path = false){
		return $this->where(array('id' => $id))->save(array('path' => $path));
	}
}
