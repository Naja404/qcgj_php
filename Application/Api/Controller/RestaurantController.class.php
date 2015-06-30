<?php
/**
 * 餐厅爬虫
 */

namespace Api\Controller;
use Think\Controller;

class RestaurantController extends ApiController {
    
    // 餐厅模型
    public $restaurantModel;

    public $snoopy;

    public $fetch;

    public function _initialize(){  
        
        parent::_initialize();

        $this->restaurantModel = D('Restaurant');

        $this->_initParam();
        $this->_initSnoopy();
    }

    /**
     * 获取餐厅内容
     *
     */
    public function getRestaurant(){

        // $url = cacheList('dianping');
        $url = "shop/18615887";

        $getUrl = 'http://m.dianping.com'.$url;

        $fetchRes = $this->snoopy->fetch($getUrl); 

        $detail = $this->fetch->fetch($fetchRes->results, 'getRestaurant');

        $detail['url'] = $url;
        echo '<pre>';
        print_r($detail);exit;
        $this->restaurantModel->add($detail);

        cacheList('dianping_back', $url);
    }

    /**
     * 下载图片
     * @param string $url 图片路径
     * @param string $fileName 图片存储路径
     */
    private function _downloadImage($url, $fileName){
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
     * 表单提交
     * @param string $url 提交地址
     * @param array $form 表单内容
     */
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
        $this->snoopy->cookies['cityid'] = 1;
        $this->snoopy->cookies['PHOENIX_ID'] = '0a010e1c-14d4c764622-493dc7';
        $this->snoopy->cookies['m_flash2'] = 1;
        $this->snoopy->cookies['pvhistory'] = '5ZWG5oi3Pjo8L3Nob3BsaXN0LzEvci80L2MvMTAvcy9zXy0xPjo8MTQzNTY1MjQ5NDc3MV1fW+i/lOWbnj46PC9hamF4L2dldExvY2F0aW9uSW5mbz9jYWxsYmFjaz1qc29ucDE+OjwxNDM1NjUyNTA1NTI3XV9b';
        $this->snoopy->cookies['testName'] = 'test2';
        $this->snoopy->host = 'm.dianping.com';
        $this->snoopy->agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';
    }

    /**
     * 初始化参数及配置信息
     *
     */
    private function _initParam(){
        import('Extend.FetchHTML');
        $this->fetch = new \FetchHTML('Restaurant');
    }

}
