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
	 * 获取图片url
	 *
	 */
	public function getRestaurantImg(){

		$img = array();

		foreach (pq('.picture-list')->find('li') as $k => $v) {
			$img[] = pq($v)->find('img')->attr('src');
		}

		return $img;
	}

	/**
	 * 获取图片url other
	 *
	 */
	public function getRestaurantImgOther(){

		$img = array();

		foreach (pq('')->find('li') as $k => $v) {

			$tmp = pq($v)->find('img')->attr('src');

			if (!empty($tmp)) {
				$img[] = $tmp;
			}
		}

		return $img;
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

		$image = pq('.pic-txt')->find('img')->attr('src');

		if (!$image) $image = pq('.new_pic')->find('img')->attr('src');

		$address = preg_replace('/\s+/', '', $arr[0][0]);

		$detail = array(
				'name'    => trim($name[0]),
				'address' => $address,
				'tel'     => is_numeric(trim($arr[1][0])) ? trim($arr[1][0]) : trim($arr[2][0]),
				'image' => $image,
			);

		return $detail;
	}

}

?>
