<?php
return array(
	'APP_GROUP_LIST' => 'Api,Web',
	'DEFAULT_MODULE' =>	'Api',
	'URL_MODEL'      =>	1,

	/*数据库设置 */
	'DB_TYPE'           =>  'mysql',
	'DB_HOST'           =>  '127.0.0.1',
	'DB_NAME'           =>  'qcgj_local',
	'DB_USER'           =>  'root',
	'DB_PWD'            =>  '',
	'DB_PORT'           =>  '3306',
	'DB_PREFIX'         =>  'tb_',
	'DB_SUFFIX'         => '',

	/* Redis设置*/
	'REDIS_HOST'          =>  '127.0.0.1',
	'REDIS_PORT'          =>  '6379',
	'REDIS_CTYPE'         => 1, //连接类型 1:普通连接 2:长连接
	'REDIS_TIMEOUT'       => 0, //连接超时时间(S) 0:永不超时

	'DATA_CACHE_TIME'     => 0,      // 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS' => false,   // 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'    => false,   // 数据缓存是否校验缓存
	'DATA_CACHE_PREFIX'   => '',     // 缓存前缀
	'DATA_CACHE_TYPE'     => 'Redis',  // 数据缓存类型,

	//开启字段映射机制
	'READ_DATA_MAP'	  =>  true,

	// 开启语言包
	'LANG_SWITCH_ON'		=>	true,	 //开启语言包功能
    'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
);
