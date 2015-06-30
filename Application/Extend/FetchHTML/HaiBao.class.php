<?php
/**
* 海报网爬虫类
*/
require_once(dirname(__FILE__).'/../phpQuery.class.php');

class HaiBao {
	
	public function __construct($filePath){
		phpQuery::newDocumentHTML($filePath); 
	}

	/**
	 * 分离logo图片及品牌名
	 *
	 */
	public function splitLogo(){
		
		$logo = array();

		foreach (pq('.b-border.margin-10')->find('a') as $k => $v) {
			$name = preg_replace('/<img(.*)alt="">/', '', pq($v)->html());
			$name = explode('<br>', $name);
			$logo[] = array(
					'name_en'  => trim($name[0]),
					'name_zh' => trim($name[1]),
					'image' => pq($v)->find('img')->attr('src'),
				);
		}

		return $logo;
	}
}

?>
