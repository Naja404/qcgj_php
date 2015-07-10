<?php
/**
 * yoka爬虫
 */

namespace Api\Controller;
use Think\Controller;

class YokaController extends ApiController {
    
    // yoka模型
    public $yokaModel;

    public $snoopy;

    public $fetch;

    public function _initialize(){  
        import('Extend.FetchHTML');
        
        parent::_initialize();

        $this->yokaModel = D('Yoka');

        $this->_initParam();
        $this->_initSnoopy();
    }

    /**
     * 获取产品url
     *
     */
    public function getProductUrl(){
        $url = "http://brand.yoka.com/cosmetics/all/product_0_0_0_0_all_0_3.htm";

        $fetchRes = $this->snoopy->fetch($url);

        makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'what1.log');

        // $what = $this->fetch->fetch(C('FETCH_INFO.FILE_PATH').'what.log', 'getProductUrl');exit('123'); 
    }


    /**
     * 
     *
     */
    private function _downloadFile($url = false, $fileName = false){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $file_content = curl_exec($ch);
        curl_close($ch);

        $downloaded_file = fopen($fileName, 'w');
        fwrite($downloaded_file, $file_content);
        fclose($downloaded_file);

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

        $this->fetch = new \FetchHTML('Yoka');

    }

}
