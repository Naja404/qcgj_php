<?php
/**
 * 景点模型
 */
namespace Api\Model;
use Think\Model;

class TravelModel extends Model{

	/**
	 * 添加景点id
	 * @param int $number 景点id
	 */
	public function addTravelNumber($number = 0){
		return $this->add(array('number' => $number));
	}

	/**
	 * 添加景点详细
	 * @param array $detail 景点详细内容
	 */
	public function addTravel($detail = array()){
		return $this->add($detail);
	}

	/**
	 * 更新图片到数据库
	 * @param array $update 更新数据
	 */
	public function upTravel($id = 0, $update = array()){
		$this->where(array('SightId' => $id))->save($update);
	}

	/**
	 * 获取列表
	 *
	 */
	public function getTravelList(){
		return $this->where("SightId != ''")->select();
	}

	public function getBrandList(){
		return $this->table(tname('brand'))->field('logo_url')->select();
	}
}
