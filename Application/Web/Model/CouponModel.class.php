<?php
/**
 * 优惠券模型
 */
namespace Web\Model;
use Think\Model;

class CouponModel extends Model{

	/**
	 * 获取优惠券列表
	 * @param integer $pageStart 开始条数
	 * @param integer $pageLength 条数
	 * @param string $search 搜索关键词
	 * @param string $fileType 数据输出类型 DT=dataTable
	 */
	public function getCoupon($pageStart = 0, $pageLength = 10, $search = false, $fileType = 'DT'){

		$field = "name, CONCAT(LEFT(begin_date, 10), '至', LEFT(end_date, 10)) AS times, status";

		$where = array(
				'name' => array('LIKE', '%'.$search.'%'),
			);
		$couponList = $this->table(tname('coupon'))
				->field($field)
				->where($where)
				->page($pageStart, $pageLength)
				->select();

		$returnCoupon = array();

		if ($fileType == 'DT') {

			$returnCoupon['draw']            = (int)I('get.draw');
			$returnCoupon['recordsTotal']    = (int)$this->table(tname('coupon'))->where()->count();
			$returnCoupon['recordsFiltered'] = (int)$this->table(tname('coupon'))->where($where)->count();
			$returnCoupon['data']            = $this->formatDataWithDT($couponList);

		}

		return $returnCoupon;
	}

	/**
	 * 格式化数据为datatables格式
	 * @param array $data
	 */
	public function formatDataWithDT($data = array()){

		$returnData = array();

		foreach ($data as $k => $v) {
			array_unshift($v, '<th class="center"><label><input type="checkbox" class="ace" /><span class="lbl"></span></label>');
			$v['status']       = L('TEXT_COUPON_STATUS_'.$v['status']);
			$v['shopCount']    = '<a href="'.U('Coupon/listview').'" target="_blank">9</a>';
			$v['receiveCount'] = '123';
			$v['useCount']     = '33';
			array_push($v, '<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons"><a class="blue" href="#"><i class="icon-zoom-in bigger-130"></i></a><a class="green" href="#"><i class="icon-pencil bigger-130"></i></a><a class="red" href="#"><i class="icon-trash bigger-130"></i></a></div>');

			array_push($returnData, array_values($v));
		}

		return $returnData;
	}
}
