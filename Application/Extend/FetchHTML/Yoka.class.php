<?php
/**
* yoka爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class Yoka {
	
	public function __construct($filePath){
		
		phpQuery::newDocumentFile($filePath); 
	}

	public function getProductUrl(){
		exit('12312');
		$arr = array();

		foreach (pq('.xindeSearch_sort_main')->find('.box') as $k => $v) {
			$arr[] = pq($v)->find('a')->attr('href');
		}

		echo '<pre>';
		print_r($arr);exit;
	}
}

?>
