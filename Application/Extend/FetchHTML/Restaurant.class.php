<?php
/**
* 餐厅爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class Restaurant {

	public function __construct($filePath){
		phpQuery::newDocumentHTML($filePath);
	}

	/**
	 * 获取餐厅详细页url
	 *
	 */
	public function getRestaurantUrl(){

		$url = array();

		foreach (pq()->find('li') as $k => $v) {
			$url[] = pq($v)->find('a')->attr('href');
		}

		return $url;
	}

	/**
	 * 获取餐厅详细内容
	 *
	 */
	public function getRestaurant(){

		foreach (pq('.details-mode.info-address')->find('a') as $k => $v) {
			$arr[] = pq($v)->text();
		}

		$name = pq('.shop-name')->text();

		$detail = array(
				'name'    => $name[0],
				'address' => preg_replace('/\s+/', '', $arr[0][0]),
				'tel'     => trim($arr[1][0]),
				'image' => pq('.pic-txt')->find('img')->attr('src'),
			);

		return $detail;
	}

}

?>
