<?php
/**
 * 公用方法
 */

/**
 * 缓存,默认Redis缓存
 * @param  mixed $name    缓存名
 * @param  mixed $value   缓存数据
 * @param  mixed $options 缓存参数
 * @return mixed
 */
function cache($name,$value = '',$options = array()){
	static $cache   =   '';
	if(empty($cache)){
		//默认memcache
		$type       =   isset($options['type'])?$options['type']:'Redis';
		$cache 	    =   Think\Cache::getInstance($type);
	}
	//获取缓存
	if($value === ''){
		return $cache->get($name);
	}elseif(is_null($value)){
		//删除缓存
		return $cache->rm($name);
	}else{
		//缓存数据
		if(is_array($options))
			$expire = $options['expire'] ? $options['expire'] : '';
		else
			$expire = is_numeric($options)	?	$options	  : '';

		return $cache->set($name,$value,$expire);
	}
}

/**
 * 设置缓存队列
 * @param string $path 队列存储路径
 * @param array $value 队列数组内容
 * @param array $options 队列参数
 * @return mixed
 */
function cacheList($path = false, $value = array(), $options = array()){
	static $cache   =   '';
	if(empty($cache)){
		//默认Redis
		$type       =   isset($options['type']) ? $options['type'] : 'Redis';
		$cache 	    =   Think\Cache::getInstance($type);
	}

	if (empty($path) || !$path) {
		return false;
	}

	if (is_array($options) && count($options) > 0) {
		if ($options['function'] = 'size') {
			return $cache->size($path);
		}
	}

	if ($value === array() && $path) {
		$results = $cache->pop($path);
		return json_decode($results, true);
	}

	return $cache->push($path, json_encode($value));
}

/**
 * 去除所有空格
 * @param string $str 字符串
 */
function trimAll($str = false){

	$search = array(" ","　","\t","\n","\r");

	$replace = array("","","","","");

	return str_replace($search, $replace, $str);
}

/**
 * 存储文本文件
 * @param text $content 文本内容
 */
function makeFetchFile($content = false, $path = null){

	Think\Log::write($content, '', '', $path, array('write_type' => 'html'));
}

/**
 * 表名前缀
 * @param string $table 表名
 */
function tname($table = false){
	return C('DB_PREFIX').$table;
}

/**
 * 创建uuid
 *
 */
function makeUUID(){

	mt_srand((double)microtime()*10000);
	$charid = strtolower(md5(uniqid(rand(), true)));
	$uuid = substr($charid, 0, 8).$hyphen
		    .substr($charid, 8, 4).$hyphen
		    .substr($charid,12, 4).$hyphen
		    .substr($charid,16, 4).$hyphen
		    .substr($charid,20,12);

	return $uuid;
}
/**
 * 检测手机号码
 * @param int $mobile 手机号
 * @return bool
 */
function checkMobileFormat($mobile = 0){
	if (!is_numeric($mobile)) {
		return false;
	}

	$preg = '/^1[34587]\d{9}$/';

	if (preg_match($preg, $mobile)) {
		return true;
	}

	return false;
}

/**
 * 记录错误日志
 * @param array $arr mixed
 * @param string $path 日志存储路径
 */
function errLog($arr = array(), $path = false){

	$level = $arr['level'] ? $arr['level'] : 'WARN';
	$path  = $path ? $path : C('LOG_PATH').date('y_m_d').'_custom.log';

	Think\Log::write(var_export($arr, true), $level, '', $path);
}

?>
