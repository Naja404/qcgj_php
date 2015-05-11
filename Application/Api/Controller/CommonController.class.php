<?php
/**
 * 公用模块
 */

namespace Api\Controller;
use Think\Controller;
use Think\Cache;

class CommonController extends Controller {

    public $cache;

    public function _initialize(){
    	$this->cache = Cache::getInstance('redis');
    }

    public function index(){
    	echo sha1('abc123123');
    }

    public function setHash(){
    	$hashArr = array(
				'name' => 'hisoka',
				'age'  => '26',
				'sex'  => 'M',
    		);
    	echo $this->cache->hset('test:php_hash', $hashArr);
    }

    public function subscribeChannel(){
    	$this->cache->subscribeMsg();
    }

    public function publishMsg(){
    	$this->cache->publishMsg('redisChat', 'what time '.time());
    }
}