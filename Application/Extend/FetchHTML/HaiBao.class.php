<?php
/**
* 海报网爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class HaiBao {
	
	public function __construct($filePath){
		phpQuery::newDocumentFile($filePath); 
	}

	/**
	 * 分离logo图片及品牌名
	 *
	 */
	public function splitLogo(){
		$what = pq('.sort-list')->find('a')->attr('href');

		echo '<pre>';
		print_r($what);exit;
	}
}

?>
