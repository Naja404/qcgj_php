<?php
/**
 * 爬虫
 *
 */
namespace Api\Controller;
use Think\Controller;
use Model\Fetch;

class FetchController extends Controller {
	// 默认城市id 1=上海,2=北京,4=广州,7=深圳
	const CITY_ID = 3;
	const CITY_ID_UUID = 'bf98a329000211e4b2bf00163e000dce';
	const DOMAIN_URL = 'm.dianping.com';

	// snoopy 实例
	public $snoopy;

	// 文件保存路径
	public $filePath;

	// 类名
	public $className;

	// 模型名
	public $fetchModel;

	/**
	 *
	 */
	protected function _initialize(){

		import('Extend.FetchHTML');

		$this->fetchModel = D('Fetch');

		$this->_initParam();
		$this->_initSnoopy();
	}

	/**
	 * 创建商圈分类爬取内容
	 *
	 */
	public function setupShopMallCateCache(){
		$shopMallCate = $this->fetchModel->getShopMallCate(self::CITY_ID_UUID);

		foreach ($shopMallCate as $k => $v) {
			$status = cacheList(C('FETCH_INFO.CACHE_SHOPMALL'), $v);
		}

		return $status;
	}

	/**
	 * 创建商圈爬取内容
	 *
	 */
    public function setupShopMallCache(){
		$shopMall = $this->fetchModel->getShopMall();

		foreach ($shopMall as $k => $v) {
			$v['round_info'] = json_decode($v['round_info'], true);
			$status = cacheList(C('FETCH_INFO.CACHE_SHOPLIST'), $v);
		}

		return $status;
	}


	/**
	 * 获取商圈分类
	 *
	 */
	public function shopMallCate(){

		$fetchUrl = 'http://m.dianping.com/shopping/malllist/c4';

		$this->downloadHTML($fetchUrl, $this->filePath);

		$fetchRes = $this->fetch->fetch($this->filePath, 'shopMallCate');

		unset($fetchRes[0]);

		return $this->fetchModel->addShopMallCate($fetchRes, $this->className, self::CITY_ID_UUID);
	}

	/**
	 * 获取商圈列表
	 *
	 */
	public function shopMallList(){

		// $res = cacheList('Fetch:List:shopMall', array('id' => 209, 'url' => '/shopping/malllist/c2r27615'));exit();

		// $cacheRes = cacheList(C('FETCH_INFO.CACHE_SHOPMALL'));

		// if (!is_array($cacheRes)) {
		// 	return false;
		// }

		// $filePath = C('FETCH_INFO.FILE_PATH').$cacheRes['url'].'.log';
		// $this->downloadHTML($this->domain.$cacheRes['url'], $filePath);

		// $mallRes = $this->fetch->fetch($filePath, 'shopMallList');

		$mallRes = array(
					// '/shop/5736189',
					// '/shop/2503993',
					// '/shop/4298929',
					// '/shop/9033118',
					// '/shop/4530331',
					// '/shop/21074577',
					'/shop/4500489',
			);

		foreach ($mallRes as $k => $v) {

			// $fileName = C('FETCH_INFO.FILE_PATH').$v['url'].'.log';
			$fileName = C('FETCH_INFO.FILE_PATH').$v.'.log';
			// $this->downloadHTML($this->domain.$v['url'], $fileName);
			$this->downloadHTML($this->domain.$v, $fileName);

			$this->shopMallDetail($fileName, $cacheRes['id']);
		}

	}

	/**
	 * 获取商圈详细内容
	 * @param string $filePath 文件路径
	 * @param int $pid 商圈所述分类id
	 */
	public function shopMallDetail($filePath = false, $pid = 0){

		$fetchRes = $this->fetch->fetch($filePath, 'shopDetail');

		$mapFilePath = C('FETCH_INFO.FILE_PATH').$fetchRes['map'].'.log';

		$this->downloadHTML($this->domain.$fetchRes['map'], $mapFilePath);
		$fetchRes['location'] = $this->fetch->fetch($mapFilePath, 'shopLocation');

		$fetchRes['cid'] = $pid;

		$fetchRes['new_round'] = json_encode($fetchRes['round_info']);
		echo '<pre>';
		print_r($fetchRes);exit;

		return $this->fetchModel->addShopMallDetail($fetchRes);
	}

	/**
	 * 获取店铺列表
	 *
	 */
	public function shopList(){

		$cacheRes = cacheList(C('FETCH_INFO.CACHE_SHOPLIST'));

		if (!is_array($cacheRes)) {
			return false;
		}

		foreach ($cacheRes['round_info'] as $k => $v) {

			$fileName = C('FETCH_INFO.FILE_PATH').$v['round_url'].'.log';
			$this->downloadHTML($this->domain.$v['round_url'], $fileName);

			$shopList = $this->fetch->fetch($fileName, 'shopList');

			// 店铺翻页数据
			if ($shopList['maxPage'] >= 2) {
				for ($i = 2; $i <= $shopList['maxPage']; $i++) {
					$ajaxUrl = $shopList['ajaxUrl'].'p'.$i;

					$savePath = str_replace($this->domain, '', $ajaxUrl);
					$shopListFileName = C('FETCH_INFO.FILE_PATH').$savePath.'.log';

					$this->downloadHTML($ajaxUrl, $shopListFileName);
					$shopListAjax = $this->fetch->fetch($shopListFileName, 'shopListWithAjax');

					$shopList['data'] = array_merge($shopList['data'], $shopListAjax);
				}
			}

			$this->fetchModel->addShop($shopList['data'], $cacheRes['id']);
		}
	}

	/**
	 * 获取优惠券分类
	 *
	 */
	public function couponCate(){

	 	$filePath = C('FETCH_INFO.FILE_PATH').'/tuan/shanghai.log';
	 	// $this->downloadHTML('http://m.dianping.com/tuan/shanghai', $filePath);
		$couponCate = $this->fetch->fetch($filePath, 'couponCate');

		$this->fetchModel->addCouponCate($couponCate, $this->className);
	}

	/**
	 * 获取折扣信息
	 *
	 */
	public function saleListWithJSON(){

		$i = $page = 1;
		$pageIndex = 1;
		$reqUrl = 'http://m.dianping.com/midas/shortpreferential/weixin/ajax/loadSPromo?lng=0&lat=0&cityid=1&regionid=0&shoptype=20&sort=4&pageCount=10000&pageType=weixin&pageIndex=%s';

		while ($page) {
			$snoopy = $this->snoopy->fetch(sprintf($reqUrl, $pageIndex));

			$saleList = json_decode($snoopy->results, true);

			$page = $saleList['msg']['pageIndex'];
			$pageIndex = $saleList['pageIndex'];

			if ($saleList['code'] != 200 || !$page) {
				echo '<pre>';
				print_r($saleList);exit;
			}

			$this->fetchModel->addSaleList($saleList['msg']['list']);
		}
	}

	/**
	 * 设置折扣信息缓存
	 *
	 */
	public function setupSaleCache(){
		$saleList = $this->fetchModel->getSaleList();
		foreach ($saleList as $k => $v) {
			cacheList(C('FETCH_INFO.CACHE_SALE'), $v);
		}
	}

	/**
	 * 获取折扣店铺信息
	 *
	 */
	public function saleShopList(){

		$cacheRes = cacheList(C('FETCH_INFO.CACHE_SALE'));

		if (!$cacheRes || !is_array($cacheRes)) {
			return false;
		}

		$url = $this->domain.$cacheRes['url'];
		$filePath = C('FETCH_INFO.FILE_PATH').'/saleshop/'.$cacheRes['id'].'.log';
		$this->downloadHTML($url, $filePath);
		$saleDetail = $this->fetch->fetch($filePath, 'saleShopDetail');

		if ($saleDetail['shopUrl']) {
			$shopUrl = $this->domain.$saleDetail['shopUrl'];
			$shopFileName = C('FETCH_INFO.FILE_PATH').'/saleshop/'.$cacheRes['id'].'_saleshop.log';
			$this->downloadHTML($shopUrl, $shopFileName);
			$shopList = $this->fetch->fetch($shopFileName, 'saleShopList');
			unset($saleDetail['shopUrl']);
		}
		$saleDetail['sid'] = $cacheRes['id'];
		$saleDetail['shopList'] = $shopList;

		echo $this->fetchModel->addSaleShopList($saleDetail);
	}

	/**
	 * 最新优惠信息
	 *
	 */
	public function saleListWithNew(){
		$cacheRes = cacheList(C('FETCH_INFO.CACHE_SALE_LAST'));

		if (count($cacheRes) <= 0 || !is_array($cacheRes)) {

			$err = array(
					'errName' => 'cache sale last empty',
				);
			errLog($err);

			$this->fetchModel->setSaleLastCache();

			return false;
		}

		$filePath = C('FETCH_INFO.FILE_PATH').'/salelist/'.time().'.log';

		$this->downloadHTML($cacheRes['url'], $filePath);

		$shopListNew = $this->fetch->fetch($filePath, 'saleListWithNew');

		$this->fetchModel->addSaleListWithLast($shopListNew);
	}

	/**
	 * 剥离商圈名
	 *
	 */
	public function splitShopMallName(){
		echo $this->fetchModel->splitShopMallName();
	}

	/**
	 * 剥离店铺名
	 *
	 */
	public function splitShopName(){
		echo $this->fetchModel->splitShopName();
	}

	/**
	 * 去除空格
	 *
	 */
	public function trimString(){
		echo $this->fetchModel->trimString();
	}
	/**
	 * 下载html
	 * @param string $url html地址
	 * @param string $saveName 文件保存地址
	 */
	public function downloadHTML($url = false, $saveName = false){
		$fetchRes = $this->snoopy->fetch($url);
		makeFetchFile($fetchRes->results, $saveName);
	}

	/**
	 * 品牌缓存数据 爬虫数据
	 *
	 */
	public function setBrandCache(){
		$this->fetchModel->setBrandCache();
	}

	/**
	 * 品牌 爬虫数据合并到qcgj
	 *
	 */
	public function setupBrand(){
		$num = 1;

		while ($num <= 500) {
			$cacheRes = cacheList(C('FETCH_INFO.CACHE_BRAND'));
			// $cacheRes = json_decode('{"id":"52580","name_zh":"\u9999\u5982\u653e\u7f8e\u98df","floor":"B1\n","tb_category_id":"c67b6f6f4437feb7460d1576ad6788de","tb_mall_id":"8202645e77796dc5ce4492068b07b8bc"}', true);


			if (!is_array($cacheRes) || count($cacheRes) <= 0) {
				echo 'no data';exit;
			}

			$this->fetchModel->setupBrand($cacheRes);
			$num++;
		}

	}

	/**
	 * 商区分类 爬虫数据合并到qcgj
	 *
	 */
	public function setTradeArea(){
		$res = $this->fetchModel->setupShopMallArea();
		echo '<pre>';
		print_r($res);exit;
	}

	/**
	 * 品牌分类 爬虫数据合并到qcgj
	 *
	 */
	public function setupBrandCategory(){
		$res = $this->fetchModel->setupBrandCategory();
		echo '<pre>';
		print_r($res);exit;
	}

	/**
	 * 商厦 爬虫数据合并到qcgj
	 *
	 */
	public function setupShopMall(){
		$res = $this->fetchModel->setupShopMall();
		echo '<pre>';
		print_r($res);exit;
	}

	/**
	 * 筛选品牌信息
	 *
	 */
	public function filterBrand(){
		$brandList = $this->fetchModel->filterBrand();
		echo '<pre>';
		print_r($brandList);exit;
	}

	/**
	 * 初始化参数及配置信息
	 *
	 */
	private function _initParam(){
		$this->className = I('get.brand', 'DianPing');

		$this->domain = I('get.brand', 'http://m.dianping.com');

		$this->filePath = C('FETCH_INFO.FILE_PATH').time().'.log';

		$this->fetch = new \FetchHTML($this->className);

	}

	/**
	 * 实例化snoopy
	 *
	 */
	private function _initSnoopy(){
		import('Extend.Snoopy');
		$this->snoopy = new \Snoopy();
		$this->snoopy->cookies['cityid'] = self::CITY_ID;
		$this->snoopy->cookies['PHOENIX_ID'] = '0a010e1c-14d4c764622-493dc7';
		$this->snoopy->cookies['m_flash2'] = 1;
		$this->snoopy->cookies['pvhistory'] = '6L+U5ZuePjo8L3Nob3AvNTczNjE4OT46PDE0MzE1MDY4OTcxMjJdX1s=';
		$this->snoopy->cookies['testName'] = 'test2';
		$this->snoopy->host = self::DOMAIN_URL;
		$this->snoopy->agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';

	}
}
