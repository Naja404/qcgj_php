<?php
return array(
	'ERR_CODE_MSG' => array(
			'40015' => '手机号格式不对',
		),

	// 爬虫配置信息
	'FETCH_INFO' => array(
			'DIANPING_DOMAIN' => 'http://m.dianping.com',
			'FILE_PATH'       => './Public/download/',
			'CACHE_SHOPMALL'  => 'Fetch:List:shopMall',
			'CACHE_SHOPLIST'  => 'Fetch:List:shopList',
			'CACHE_SALE'      => 'Fetch:List:saleList',
			'CACHE_SALE_LAST' => 'Fetch:List:saleLast',
			'CACHE_BRAND'     => 'Fetch:List:brand',
		),

	// 优惠爬虫url列表
	'SALE_LIST_URL' => array(
			'http://m.dianping.com/midas/shortpreferential/weixin/loadShortPromoList?cityid=1&shoptype=20&regionid=0&sort=3',// 购物
			'http://m.dianping.com/midas/shortpreferential/weixin/loadShortPromoList?cityid=1&shoptype=50&regionid=0&sort=3',// 丽人
		),
);