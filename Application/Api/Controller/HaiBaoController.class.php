<?php
/**
 * 海报网爬虫
 */

namespace Api\Controller;
use Think\Controller;

class HaiBaoController extends ApiController {
    
    // 海报模型
    public $haibaoModel;

    public $snoopy;

    public $fetch;

    public function _initialize(){  
        import('Extend.FetchHTML');
        
        parent::_initialize();
        // $this->userModel = D('HaiBao');
        $this->_initParam();
        $this->_initSnoopy();
    }

    public function splitLogo(){
        for ($i=1; $i < 33; $i++) { 
            $cache = cache('HaiBao:logo:'.$i);

            $this->fetch->fetch($cache, 'splitLogo');exit();
        }
    }

    /**
     * 爬logo图片
     *
     */
    public function fetchLogo() {
        
        $url = 'http://brands.haibao.com/brand/ajax/';

        for ($i=1; $i < 33; $i++) { 
            $form = array(
                    'type' => 1,
                    'az'   => 'all',
                    'page' => $i,
                );
            
            $html = $this->_submitForm($url, $form);

            cache('HaiBao:logo:'.$i, $html);
        }

    }

    private function _submitForm($url, $form = array()){
        
        $this->snoopy->submit($url, $form);

        return $this->snoopy->results;
    }
    /**
     * 实例化snoopy
     *
     */
    private function _initSnoopy(){
        import('Extend.Snoopy');
        $this->snoopy = new \Snoopy();
        $this->snoopy->agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';
    }

    /**
     * 初始化参数及配置信息
     *
     */
    private function _initParam(){

        $this->fetch = new \FetchHTML('HaiBao');

    }

}
