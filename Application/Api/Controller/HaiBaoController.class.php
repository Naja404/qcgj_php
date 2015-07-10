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

        $this->haibaoModel = D('HaiBao');

        $this->_initParam();
        $this->_initSnoopy();
    }

    public function getFile(){
        $address = file('/Users/hisoka/WorkSpace/XinYi/qcgj_php/shanghaiMallAddress');
        $mallen = file('/Users/hisoka/WorkSpace/XinYi/qcgj_php/shanghaiMallZh.php');
        $mallzh = file('/Users/hisoka/WorkSpace/XinYi/qcgj_php/shanghaiMallEn.php');
        $floor = file('/Users/hisoka/WorkSpace/XinYi/qcgj_php/shanghaiMallFloor');
        $mall = file('/Users/hisoka/WorkSpace/XinYi/qcgj_php/shanghaiMall.php');
        
        foreach ($mall as $key => $value) {

            if (!empty($mallzh[$key])) {
                $id = $this->haibaoModel->table('tb_brand')->where(array('name_zh' => $mallzh[$key]))->getField('id');
                $mallId = $this->haibaoModel->table('tb_brand_mall')->where(array('tb_brand_id' => $id))->getField('tb_mall_id');

                $hasId = $this->haibaoModel->table('tb_mall')->where(array('id' => $mallId))->getField('id');

                if ($hasId) {
                    echo $mallzh[$key];exit;
                }
            }
        }
    }

    public function setBrand(){
        $text = @file_get_contents('/Users/hisoka/Desktop/brand_tag.txt');

        $text = explode(';', $text);

        echo '<pre>';
        print_r($text);exit;
    }

    /**
     * 设置logo list
     *
     */
    public function setLogoList(){
        $logoList = $this->haibaoModel->getLogoList();

        foreach ($logoList as $k => $v) {
            if ($k == 10) {
                break;
            }
            cacheList('logo_test', $v);
        }
    }

    /**
     * 抓取logo信息
     *
     */
    public function splitLogo(){
        for ($i=1; $i < 33; $i++) { 
            $cache = cache('HaiBao:logo:'.$i);

            $logo = $this->fetch->fetch($cache, 'splitLogo');
            
            foreach ($logo as $k => $v) {
                $this->haibaoModel->setLogo($v);
            }   
        }
    }

    /**
     * 下载图片文件
     *
     */
    public function downloadLogo(){
        // $logoList = $this->haibaoModel->getLogoList();


        // foreach ($logoList as $k => $v) {
            $v = cacheList('logo');
            $res = $this->_formatLogoUrl($v['image']);
            $fileName = '/Users/hisoka/WorkSpace/XinYi/qcgj_php/Public/download/logo_other/'.$res['fileName'];
            $this->_downloadFile($res['url'], $fileName);

            $this->haibaoModel->upLogoPath($v['id'], $fileName);
            // exit('===');
            // sleep(1);
        // }
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
        // $data = @file_get_contents($url);

        // file_put_contents($fileName, $data);
    }

    /**
     * 格式化logo url
     * @param string $url logo url
     */
    private function _formatLogoUrl($url = false){
        $url = str_replace('92_92', '200_200', $url);

        preg_match('/\w+.png/', $url, $preg);

        return array('url' => $url, 'fileName' => $preg[0]);
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
