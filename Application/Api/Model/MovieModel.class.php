<?php
/**
 * 电影模型
 */
namespace Api\Model;
use Think\Model;

class MovieModel extends Model{

	/**
	 * 添加电影院
	 * @param array $cinema 电影院数据
	 */
	public function addCinema($cinema = array()){
		$add = array(
				'name'    => $cinema['name'],
				'address' => $cinema['address']['address'],
				'area'    => $cinema['address']['area'],
				'image'   => $cinema['image'],
				'url'     => $cinema['url'],
			);

		return $this->add($add);
	}

	/**
	 * 获取电影院列表
	 * @param array $where  条件
	 */
	public function getCinemaList($where = array()){
		
		if (count($where)) {
			return $this->where($where)->select();
		}
		
		return $this->select();
	}

	/**
	 * 更新电影院详细信息
	 * @param int $id 
	 * @param array $roundInfo 周边信息
	 */
	public function upRoundInfo($id = 0, $roundInfo = array()){
		return $this->where(array('id' => $id))->save(array('text' => json_encode($roundInfo)));
	}
}
