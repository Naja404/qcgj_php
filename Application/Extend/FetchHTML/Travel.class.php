<?php
/**
* 景点爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class Travel {
	
	public function __construct($filePath){
		phpQuery::newDocumentFile($filePath); 
	}

	/**
	 * 获取景点详细url
	 *
	 */
	public function getTravelUrl(){
		
		$urlArr = array();
		
		foreach (pq()->find('a') as $k => $v) {
			$url = pq($v)->attr('href');

			if (in_array($url, $urlArr)) continue;

			$urlArr[] = $url;
		}

		return $urlArr;
	}

}

?>
