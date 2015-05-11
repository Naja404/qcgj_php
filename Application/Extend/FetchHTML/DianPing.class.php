<?php
/**
* 大众点评爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class DianPing {
	
	public function __construct($filePath){
		phpQuery::newDocumentFile($filePath); 
	}

	/**
	 * 爬虫
	 *
	 */
	public function fetch(){

	}

	/**
	 * 获取商圈分类
	 *
	 */
	public function shopMallCate(){

		$menuContent = pq('.menu.main')->html();
		$subMenuContent = pq('.menu.sub')->find('div')->html();
		preg_match_all('/<a[^>]+href="(.*)">(.*)<\/a>/', $subMenuContent, $matches);

		$newMenu = array();
		$i = 0;

		foreach ($matches[2] as $k => $v) {
			if (preg_match('/全境/', $v)) {
				$i++;
				$newMenu[$i] = array(
						'name' => $v,
						'url' => $matches[1][$k],
					);
			}else{
				$newMenu[$i]['data'][] = array(
						'name' => $v,
						'url' => $matches[1][$k],
					);
			}
		}

		return $newMenu;
	}

	/**
	 * 商圈列表
	 *
	 */
	public function shopMallList(){

		$mallList = array();

		foreach (pq('.search-list.J_list')->find('li') as $k => $v) {
			$mallList[] = array(
					'name' => implode('', trimAll(pq($v)->find('h3')->text())),
					'url' => pq($v)->find('a')->attr('href'),
				);
		}

		return $mallList;
	}

	/**
	 * 获取店铺信息
	 *
	 */
	public function shopDetail(){

		foreach (pq('.brand.Fix')->eq(0)->find('li') as $k => $v) {
			$roundInfo[] = array(
					'round_name' => implode('', pq($v)->find('p')->eq(0)->text()),
					'round_url' => pq($v)->find('a')->attr('href'),
				);
		}

		$address = pq('.info-list.link-list')->eq(0)->find('a')->text();

		$mapLink = pq('.item')->attr('href');

		$title = pq('.shop-name')->html();

		$shopDetail = array(
				'name'       => $title,
				'address'    => trimAll($address[0]),
				'map'        => $mapLink,
				'round_info' => $roundInfo,
			);

		return $shopDetail;
	}

	/**
	 * 获取商圈经纬度坐标
	 *
	 */
	public function shopLocation(){
		$latlng = pq()->find('script')->html();
		preg_match_all('/(lat:|lng:)(.*),/', $latlng, $match);

		return array(
				'lat' => $match[2][0],
				'lng' => $match[2][1],
			);
	}

	/**
	 * 获取店铺列表
	 *
	 */
	public function shopList(){
		
		$shopList = array();

		foreach (pq('.result-list')->find('li') as $k => $v) {
			
			$categary = pq($v)->find('.intro.Fix > span')->text();
			$categaryName = $categary[1];

			if (preg_match('/\d+/', $categary[0])) {
				$floor = $categary[0];
			}else{
				$categaryName = $categary[0];
			}

			$shopList['data'][] = array(
					'name'          =>  trim(implode('', pq($v)->find('h3')->text())),
					'url'           => pq($v)->find('a')->attr('href'),
					'img'           => pq($v)->find('img')->attr('lazy-src'),
					'categary_name' => trimAll($categaryName),
					'floor'         => $floor,
				);
		}

		$page = pq()->find('script')->html();
		preg_match_all('/(ajaxUrl:\s+"|startPage:\s+"|maxPage:\s+")(.*)"/', $page, $match);

		$shopList['ajaxUrl']   = $match[2][0];
		$shopList['startPage'] = $match[2][1];
		$shopList['maxPage']   = $match[2][2];

		return $shopList;
	}

	/**
	 * 从ajax获取店铺列表
	 *
	 */
	public function shopListWithAjax(){
		$shopList = array();
		foreach (pq()->find('li') as $k => $v) {
			
			$categary = pq($v)->find('.intro.Fix > span')->text();
			$categaryName = $categary[1];

			if (preg_match('/\d+/', $categary[0])) {
				$floor = $categary[0];
			}else{
				$categaryName = $categary[0];
			}

			$shopList[] = array(
					'name'          =>  trim(implode('', pq($v)->find('h3')->text())),
					'url'           => pq($v)->find('a')->attr('href'),
					'img'           => pq($v)->find('img')->attr('lazy-src'),
					'categary_name' => trimAll($categaryName),
					'floor'         => $floor,
				);
		}

		return $shopList;
	}

	/**
	 * 获取优惠券分类
	 *
	 */
	public function couponCate(){

		$cate = pq('.menu.sub.Hide')->find('div')->text();

		$couponCate = array();

		foreach (pq('.menu.sub.Hide')->find('div') as $k => $v) {

			foreach (pq($v)->find('a') as $j => $m) {
				if ($j == 0) {
					continue;
				}
				$childCate[] = array(
						'name' => preg_replace('/\d+/', '', implode('', pq($m)->text())),
						'url' => pq($m)->attr('href'),
					);
			}

			$couponCate[] = array(
					'name' => preg_replace('/\d+/', '', implode('', pq($v)->find('a')->eq(0)->text())),
					'url' => pq($v)->find('a')->attr('href'),
					'child' => $childCate,
				);
			unset($childCate);
		}

		return $couponCate;
	}

	/**
	 * 获取最新折扣信息
	 *
	 */
	public function saleListWithNew(){

		$saleList = array();

		foreach (pq('.promo-list > a') as $k => $v) {
			$saleList[] = array(
					'shopName'    => implode('', pq($v)->find('.title')->text()),
					'shopContent' => implode('', pq($v)->find('.content')->text()),
					'class'       => implode('', pq($v)->find('.class')->text()),
					'tag'         => implode('', pq($v)->find('.tag')->text()),
					'shopPic'     => pq($v)->find('img')->attr('lazy-src'),
					'url'         => pq($v)->attr('href'),
				);
		}

		return $saleList;
	}

	/**
	 * 获取折扣详细信息
	 *
	 */
	public function saleShopDetail(){

		$shopDetail = array(
				'expireDate' => implode('', pq('.date')->text()),
				'rule'       => trim(implode('', pq('.detail-rule')->text())),
				'shopUrl'    => pq('.all')->attr('href'),
			);

		return $shopDetail;
	}

	/**
	 * 获取折扣店铺信息
	 *
	 */
	public function saleShopList(){
		$saleShopList = array();

		foreach (pq('.item') as $k => $v) {
			$saleShopList[] = array(
					'name'    => implode('', pq($v)->find('h3')->text()),
					'address' => implode('', pq($v)->find('.address')->text()),
					'tel'     => implode('', pq($v)->find('.phoneNo')->text()),
				);
		}
		
		return $saleShopList;
	}
}

?>