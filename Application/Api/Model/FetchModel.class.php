<?php
/**
 * 爬虫模型
 */
namespace Api\Model;
use Think\Model;

class FetchModel extends Model{

	protected $autoCheckFields = false;

	protected $city_name = array(
					'391db7b8fdd211e3b2bf00163e000dce' => '上海',
					'bd21203d001c11e4b2bf00163e000dce' => '北京',
					'bf98a329000211e4b2bf00163e000dce' => '广州',
			);

	/**
	 * 添加优惠券分类
	 * @param array $data 优惠券分类数据
	 * @param string $sourceTyep 数据来源 DianPing=大众点评
	 */
	public function addCouponCate($data = array(), $sourceType = 'DianPing'){

		if (count($data) <= 0) {
			return false;
		}

		foreach ($data as $k => $v) {
			$parentData = array(
					'pid' => 0,
					'name' => $v['name'],
					'url' => $v['url'],
					'source' => $sourceType,
				);
			$pid = $this->table(tname('fetch_coupon_cate'))->add($parentData);

			if ($pid <= 0) {
				continue;
			}

			foreach ($v['child'] as $j => $m) {
				$childData = array(
						'pid' => $pid,
						'name' => $m['name'],
						'url' => $m['url'],
					);
				$childID = $this->table(tname('fetch_coupon_cate'))->add($childData);
			}
		}

		return $childID ? true : false;
	}

	/**
	 * 添加最新折扣信息
	 * @param array $data 折扣信息
	 */
	public function addSaleListWithLast($data = array()){
		if (count($data) <= 0 || !is_array($data)) {
			return false;
		}

		foreach ($data as $k => $v) {

			if ($this->existsSaleInfo($v)) {
				$data['dataErrNum'] = $v['url'];
				errLog($data);
				return false;
			}

			$id = $this->table(tname('fetch_sale_list'))->add($v);
			if ($id) {
				$v['id'] = $id;
				cacheList(C('FETCH_INFO.CACHE_SALE'), $v);
			}
		}
	}

	/**
	 * 设置最新优惠信息cache
	 *
	 */
	public function setSaleLastCache(){
		$salePageUrl = C('SALE_LIST_URL');

		if (!is_array($salePageUrl) || count($salePageUrl) <= 0) {
			return false;
		}

		foreach ($salePageUrl as $k => $v) {
			$cacheArr = array(
					'url'     => $v,
					'created' => date('Y-m-d H:i:s'),
				);

			cacheList(C('FETCH_INFO.CACHE_SALE_LAST'), $cacheArr);

			unset($cacheArr);
		}

		return true;
	}

	/**
	 * 添加折扣信息
	 * @param array $data 折扣信息内容
	 */
	public function addSaleList($data = array()){
		if (count($data) <= 0) {
			return false;
		}

		foreach ($data as $k => $v) {
			$id = $this->table(tname('fetch_sale_list'))->add($v);
		}

		return $id ? true : false;
	}

	/**
	 * 添加折扣店铺信息
	 * @param array $data 折扣店铺信息
	 */
	public function addSaleShopList($data = array()){

		if ($data['expireDate'] || $data['rule']) {
			$sale = array(
					'expireDate' => $data['expireDate'],
					'rule'       => $data['rule'],
				);
			$this->table(tname('fetch_sale_list'))->where('id = '.$data['sid'])->save($sale);
		}

		if (count($data['shopList']) <= 0) {
			return false;
		}

		foreach ($data['shopList'] as $k => $v) {
			$saleShop = array(
					'sid'     => $data['sid'],
					'name'    => $v['name'],
					'address' => $v['address'],
					'tel'     => $v['tel'],
				);

			if ($this->existsSaleShop($saleShop)) {

				$err = array(
						'dataErrNum' => 'exists sale shop',
						'data'       => $saleShop,
					);

				errLog($err);
				continue;
			}

			$id = $this->table(tname('fetch_sale_shop'))->add($saleShop);
		}

		return $id ? true : false;
	}

	/**
	 * 获取折扣信息
	 *
	 */
	public function getSaleList(){
		return $this->table(tname('fetch_sale_list'))->select();
	}

	/**
	 * 添加商圈
	 * @param array $data 商圈数组
	 * @param string  $sourceType 来源标识 大众点评='DianPing', 美团="MeiTuan"
	 * @param string $cityID 城市id uuid
	 * @return bool
	 */
	public function addShopMallCate($data = array(), $sourceType = 'DianPing', $cityID = '391db7b8fdd211e3b2bf00163e000dce'){

		foreach ($data as $k => $v) {
			$parentData = array(
					'city_id' => $cityID,
					'pid'    => 0,
					'name'   => $v['name'],
					'url'    => $v['url'],
					'source' => $sourceType,
				);

			$pid = $this->_addShopMallCate($parentData);

			foreach ($v['data'] as $j => $m) {
				$childData = array(
						'city_id' => $cityID,
						'uuid'   => makeUUID(),
						'pid'    => $pid,
						'name'   => $m['name'],
						'url'    => $m['url'],
						'source' => $sourceType,
					);
				$status = $this->_addShopMallCate($childData);
			}
		}

		return $status ? true : false;
	}

	/**
	 * 添加商圈详细内容
	 * @param array $data 商圈详细数据内容
	 */
	public function addShopMallDetail($data = array()){

		if ($this->existsShopMall($data)) {
			$err = array(
					'dataErrNum' => 'FetchModel/210',
					'ErrMsg'     => 'exists shop mall',
				);

			errLog($err);

			return;
		}

		$detail = array(
				'cid'        => $data['cid'],
				'name'       => $data['name'],
				'name_s'     => $this->pregBracket($data['name']),
				'url'        => $data['map'],
				'address'    => $data['address'],
				'address_s'  => $this->pregBracket($data['address']),
				'lat'        => $data['location']['lat'],
				'lng'        => $data['location']['lng'],
				'round_info' => json_encode($data['round_info']),
			);

		return $this->table(tname('fetch_shopmall_detail'))->add($detail);

	}

	/**
	 * 添加商铺
	 * @param array $data 商铺数据内容
	 * @param int $shopmallID 商圈id
	 * @return bool
	 */
	public function addShop($data = array(), $shopmallID = 0){

		foreach ($data as $k => $v) {
			$v['shopmall_id'] = (int)$shopmallID;
			$v['name_s'] = $this->pregBracket($v['name']);
			$v['mark'] = 2;

			if ($this->existsShop($v)) {
				$err = array(
						'dataErrNum' => 'FetchModel/249',
						'ErrMsg'     => 'exists shop',
						'data'       => $v,
					);

				errLog($err);
				continue;
			}

			$shopID = $this->table(tname('fetch_shop'))->add($v);
		}

		return $shopID ? true : false;
	}

	/**
	 * 获取商圈分类内容
	 * @param string $cityID 城市id
	 */
	public function getShopMallCate($cityID = 'bd21203d001c11e4b2bf00163e000dce'){
		$where = array(
				'pid'     => array('neq', 0),
				'city_id' => $cityID,
			);
		return $this->table(tname('fetch_shopmall'))->where($where)->select();
	}

	/**
	 * 获取商圈内容
	 *
	 */
	public function getShopMall(){

		$where = array(
				'mark' => 1,
			);
		return $this->table(tname('fetch_shopmall_detail'))->where($where)->group('url')->order('id ASC')->select();
	}

	/**
	 * 是否存在优惠信息
	 * @param array $queryData 优惠信息数组
	 * @return bool
	 */
	public function existsSaleInfo($data = array()){

		$where = array(
				'url'         => $data['url'],
				'shopContent' => $data['shopContent'],
				'shopName'    => $data['shopName'],
			);

		$count = $this->table(tname('fetch_sale_list'))->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 是否存在店铺
	 * @param array $queryData 店铺内容
	 */
	public function existsSaleShop($queryData = array()){
		$where = array(
				'sid'     => $queryData['sid'],
				'name'    => $queryData['name'],
				'address' => $queryData['address'],
			);

		$count = $this->table(tname('fetch_sale_shop'))->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 是否存在商场
	 * @param array $data 商场数据内容
	 * @return bool
	 */
	public function existsShopMall($data = array()){
		$where = array(
					// 'name'    => $data['name'],
					// 'address' => $data['address'],
					'url'     => $data['map'],
			);

		$count = $this->table(tname('fetch_shopmall_detail'))->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 是否存在店铺
	 * @param array $data 店铺数据内容
	 * @return bool
	 */
	public function existsShop($data = array()){
		$where = array(
					'shopmall_id' => $data['shopmall_id'],
					'name'        => $data['name'],
			);

		$count = $this->table(tname('fetch_shop'))->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 剥离店铺名
	 *
	 */
	public function splitShopName(){
		$shopMall = $this->table(tname('fetch_shop'))->select();

		foreach ($shopMall as $k => $v) {
			$pregName = $this->pregBracket($v['name']);
			$update = array(
					'name_s'    => $pregName,
				);

			$status = $this->table(tname('fetch_shop'))->where(array('id' => $v['id']))->save($update);
		}

		return $status;
	}

	/**
	 * 字符串去空
	 * @param string $str
	 */
	public function trimString($str = false){
		$shopList = $this->table(tname('fetch_shop'))->select();

		foreach ($shopList as $k => $v) {
			$update = array(
					'floor' => trim($v['floor']),
				);
			$status = $this->table(tname('fetch_shop'))->where(array('id' => $v['id']))->save($update);
		}

		return $status;
	}

	/**
	 * 剥离商圈名
	 *
	 */
	public function splitShopMallName(){
		$shopMall = $this->table(tname('fetch_shopmall_detail'))->select();

		foreach ($shopMall as $k => $v) {
			$pregName = $this->pregBracket($v['name']);
			$pregAddress = $this->pregBracket($v['address']);
			$update = array(
					'id'        => $v['id'],
					'name_s'    => $pregName,
					'address_s' => $pregAddress,
				);

			$status = $this->table(tname('fetch_shopmall_detail'))->where(array('id' => $v['id']))->save($update);
		}

		return $status;
	}

	/**
	 * 匹配括号内容
	 * @param string $str 匹配字符串
	 */
	public function pregBracket($str = false){

		preg_match_all('/\（(.*)\）/', $str, $pregStr);

		if (!$pregStr[0][0]) {
			preg_match_all('/\((.*)\)/', $str, $pregStr);
			if (!$pregStr[0][0]) {
				return $str;
			}
		}

		return str_replace($pregStr[0][0], '', $str);
	}

	/**
	 * 字符串转码
	 * @param string $str 字符串
	 */
	public function strToUTF($str = false){
		$encode = mb_detect_encoding($str, array('ASCII','UTF-8','GB2312','GBK'));
		if ($encode != 'UTF-8'){
			$str = iconv('UTF-8', $encode, $str);
		}

		return $str;
	}

	/**
	 * 设置商圈
	 *
	 */
	public function setupShopMallArea(){
		$oldShopMallArea = $this->table(tname('fetch_shopmall'))->where('pid != 0')->group('url')->select();

		$success = $failed = 0;

		foreach ($oldShopMallArea as $k => $v) {
			$add = array(
					'id'            => makeUUID(),
					'fetch_mall_id' => $v['id'],
					'name_zh'       => $v['name'],
					'create_time'   => date('Y-m-d H:i:s'),
					'update_time'   => date('Y-m-d H:i:s'),
					'tb_city_id'    => $v['city_id'],
					'city_name'     => $this->city_name[$v['city_id']],
				);

			$id = $this->table('qcgj_trade_area')->add($add);
			$this->table(tname('fetch_shopmall'))->where(array('id' => $v['id']))->save(array('uuid' => $add['id']));

			$id ? $success++ : $failed++;
		}

		$returnRes = array(
				'success' => $success,
				'failed' => $failed,
				'count' => count($oldShopMallArea),
			);

		return $returnRes;
	}

	/**
	 * 设置商场
	 *
	 */
	public function setupShopMall(){
		// $where = array(
		// 		'a.name_s' => array('neq', ''),
		// 		'a.url' => array('neq', ''),
		// 		'a.cid' => array('eq', 'b.id'),
		// 	);
		// $oldShopMall = $this->table('tb_fetch_shopmall_detail a, tb_fetch_shopmall b')
		// 					->where($where)
		// 					->group('a.url')
		// 					->select();

		$sql = "SELECT a.name_s,
					   a.cid,
					   a.address_s,
					   a.lng,
					   a.lat,
					   b.name as t_name,
					   b.city_id,
					   b.uuid
					FROM tb_fetch_shopmall_detail a,tb_fetch_shopmall b WHERE a.name_s <> '' AND a.url <> '' AND a.cid = b.id GROUP BY a.url";
		$oldShopMall = $this->query($sql);

		foreach ($oldShopMall as $k => $v) {
			$add = array(
					'id'               => makeUUID(),
					'name_zh'          => $v['name_s'],
					'address'          => $v['address_s'],
					'create_time'      => date('Y-m-d H:i:s'),
					'update_time'      => date('Y-m-d H:i:s'),
					'tb_trade_area_id' => $v['uuid'],
					'trade_area_name'  => $v['t_name'],
					'tb_city_id'       => $v['city_id'],
					'city_name'        => $this->city_name[$v['city_id']],
					'longitude'        => $v['lng'],
					'latitude'         => $v['lat'],
					'open_time'        => '10:00',
					'close_time'       => '22:00',
				);

			$id = $this->table('qcgj_mall')->add($add);
			$this->table(tname('fetch_shopmall_detail'))->where(array('id' => $v['id']))->save(array('uuid' => $add['id']));
			$id ? $success++ : $failed++;
		}

		$returnRes = array(
				'success' => $success,
				'failed' => $failed,
				'count' => count($oldShopMallArea),
			);

		return $returnRes;
	}

	/**
	 * 品牌分类
	 *
	 */
	public function setupBrandCategory(){
		$category = $this->table(tname('fetch_shop'))->group('categary_name')->select();

		foreach ($category as $k => $v) {
			$add = array(
					'id'          => makeUUID(),
					'name'        => $v['categary_name'],
					'create_time' => date('Y-m-d H:i:s'),
					'update_time' => date('Y-m-d H:i:s'),
					'level'       => 1,
				);

			if ($this->existsCate($add)) {
				continue;
			}

			$id = $this->table('qcgj_category')->add($add);
		}

		return $id;
	}

	public function setBrandCache(){

		$sql = "select
						a.id,
						a.name_s as name_zh,
						a.floor,
						d.id as tb_category_id,
						b.uuid as tb_mall_id
					 from
					tb_fetch_shop as a
					left join tb_fetch_shopmall_detail as b on  b.id = a.shopmall_id
					left join qcgj_mall as c on c.address = b.address_s
					left join `qcgj_category` as d on d.name = a.categary_name
					where a.mark = 2
					group by a.url";
				$brandList = $this->query($sql);
		// $brandList = $this->query('select
		// 								a.id,
		// 								a.name_s as name_zh,
		// 								a.floor,
		// 								d.id as tb_category_id,
		// 								c.id as tb_mall_id
		// 							 from
		// 							tb_fetch_shop as a
		// 							left join tb_fetch_shopmall_detail as b on  b.id = a.shopmall_id
		// 							left join qcgj_mall as c on c.address = b.address_s
		// 							left join `qcgj_category` as d on d.name = a.categary_name
		// 							group by a.url');

		foreach ($brandList as $k => $v) {
			cacheList(C('FETCH_INFO.CACHE_BRAND'), $v);
		}
	}

	/**
	 * 设置品牌及关联内容
	 * @param array $brandList 品牌数组内容
	 */
	public function setupBrand($brandList = array()){
		$v = $brandList;

		$brandID = $this->existsBrand($v['name_zh']);

		$brand = array(
				'name_zh'     => $v['name_zh'],
				'create_time' => date('Y-m-d H:i:s'),
				'update_time' => date('Y-m-d H:i:s'),
				'oper'        => 'zhangxin',
			);

		if ($brandID) {
			$brand['id'] = $brandID;
			$id = true;
		}else{
			$brand['id'] = makeUUID();
			$id = $this->table('qcgj_brand')->add($brand);
		}





		if ($id && $brand['id']) {
			$brandCate = array(
					'id'             => makeUUID(),
					'create_time'    => date('Y-m-d H:i:s'),
					'update_time'    => date('Y-m-d H:i:s'),
					'tb_brand_id'    => $brand['id'],
					'tb_category_id' => $v['tb_category_id'],
				);

			if ($this->existsBrandCate($brandCate) === false) {
				$brandCateID = $this->table('qcgj_brand_category')->add($brandCate);
			}

			$brandMall = array(
					'id'          => makeUUID(),
					'tb_brand_id' => $brand['id'],
					'tb_mall_id'  => $v['tb_mall_id'],
					'create_time' => date('Y-m-d H:i:s'),
					'update_time' => date('Y-m-d H:i:s'),
					'address'     => trim($v['floor']),
				);

			if ($this->existsBrandMall($brandMall) === false) {
				$brandMallID = $this->table('qcgj_brand_mall')->add($brandMall);
			}
		}

		$err = array(
				'data'          => $v,
				'id'            => $id,
				'brand_id'      => $brand['id'],
				'brandCateID'   => $brandCateID,
				'brand_cate_id' => $brandCate['id'],
				'brandMallID'   => $brandMallID,
				'brand_mall_id' => $brandMall['id'],
			);
		errLog($err);
	}

	/**
	 * 检测品牌分类是否存在
	 * @param string $brandCate
	 */
	public function existsBrandCate($brandCate = array()){
		if (!$brandCate) {
			return false;
		}

		$where = array(
				'tb_brand_id'    => $brandCate['tb_brand_id'],
				'tb_category_id' => $brandCate['tb_category_id'],
			);

		$count = $this->table('qcgj_brand_category')->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 检测分类是否存在
	 * @param string $brandCate
	 */
	public function existsCate($brandCate = array()){
		if (!$brandCate) {
			return false;
		}

		$where = array(
				'name'    => $brandCate['name'],
			);

		$count = $this->table('qcgj_category')->where($where)->count();

		return $count > 0 ? true : false;
	}

	/**
	 * 检测品牌是否存在
	 * @param array $brandName 品牌名
	 */
	public function existsBrand($brandName = false){
		if (!$brandName) {
			return false;
		}

		$brandID = $this->table('qcgj_brand')->where(array('name_zh' => $brandName))->getField('id');

		return  $brandID ? $brandID : false;
	}

	/**
	 * 检测品牌商场是否存在
	 * @param array $brandMall
	 */
	public function existsBrandMall($brandMall = array()){
		if (!$brandMall) {
			return false;
		}

		$where = array(
				'tb_brand_id' => $brandMall['tb_brand_id'],
				'tb_mall_id'  => $brandMall['tb_mall_id'],
			);

		$count = $this->table('qcgj_brand_mall')->where($where)->count();

		return $count > 0 ? true : false;
	}

	public function filterBrand(){
		$sql = "select a.id, a.name_zh, a.address,b.url,b.id,
				(select count(*) from qcgj_brand_mall where tb_mall_id = a.id) as mall_count from qcgj_mall as a
				left join tb_fetch_shopmall_detail as b on b.address_s = a.address
				order by a.name_zh ASC";
		$brandList = $this->query($sql);

		$newBrandList = array();
		foreach ($brandList as $k => $v) {
			preg_match_all('/\d+/', $v['url'], $brandID);
			$v['url'] = '<a href="http://m.dianping.com/shop/'.$brandID[0][0].'" target="_blank">m.dianing.com/shop/'.$brandID[0][0].'</a>';

			if ($v['mall_count'] <= 0) {
				array_unshift($newBrandList, $v);
			}else{
				array_push($newBrandList, $v);
			}
		}

		return $newBrandList;
	}

	/**
	 * 存储商圈数据
	 * @param array $data 商圈数据
	 */
	private function _addShopMallCate($data = array()){
		return $this->table(tname('fetch_shopmall'))->add($data);
	}
}
