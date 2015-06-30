<?php
/**
 * 电影爬虫
 */

namespace Api\Controller;
use Think\Controller;

class MovieController extends ApiController {
    
    // 电影模型
    public $movieModel;

    public $snoopy;

    public $fetch;

    public function _initialize(){  
        
        parent::_initialize();

        $this->movieModel = D('Movie');

        $this->_initParam();
        $this->_initSnoopy();
    }

    /**
     * 设置格瓦拉图片cache
     *
     */
    public function setGewaraImage(){
        $list = $this->movieModel->getCinemaList();

        foreach ($list as $k => $v) {
            cacheList('Gewara', $v);
        }
    }

    /**
     * 下载格瓦拉图片
     * 
     */
    public function downGewaraImage(){

        $cache = cacheList('Gewara');

        preg_match('/\w+.jpg/', $cache['image'], $imageName);

        $this->_downloadImage($cache['image'], C('FETCH_INFO.FILE_PATH').'movie/logo/'.$imageName[0]);

        $imageArr = json_decode($cache['text'], true);
        
        $url = "http://img.gewara.cn/";

        foreach ($imageArr['img'] as $k => $v) {
            preg_match('/\w+.jpg/', $v, $imageName);
            $this->_downloadImage($url.$v, C('FETCH_INFO.FILE_PATH').'movie/image/'.$imageName[0]);
        }
    }

    /**
     * 格瓦拉电影院
     *
     */
    public function getGewaraCinema(){
        $url = "http://www.gewara.com/shanghai/cinemalist";
        
        for ($i=0; $i <= 19; $i++) {

            $fetchRes = $this->snoopy->fetch($url.'?pageNo='.$i);

            makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'movie/movie'.$i.'.log');
        }
    }

    /**
     * 格瓦拉电影院详细内容
     *
     */
    public function getGewaraCinemaDetail(){
        $list = $this->movieModel->getCinemaList();
        
        $url = "http://www.gewara.com";

        foreach ($list as $k => $v) {
            $fetchRes = $this->snoopy->fetch($url.$v['url']);

            makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'movie/movieDetail'.($k+1).'.log');
        }
    }

    /**
     * 格式化格瓦拉电影院到数据库
     *
     */
    public function getGewaraCinemaDetailToData(){
        $list = $this->movieModel->getCinemaList();

        foreach ($list as $k => $v) {
            
            $filePath = C('FETCH_INFO.FILE_PATH').'movie/movieDetail'.($k+1).'.log';
            $res = $this->fetch->fetch($filePath, 'getGewaraCin`emaDetail');
            
            preg_match('/(?<=\/)\d+(?=#traffic)/', $v['url'], $cinemaId);
            
            $image = $this->_getGewaraCinemaImg($cinemaId[0]);

            $res['img'] = $image;

            $this->movieModel->upRoundInfo($v['id'], $res);
        }
    }

    /**
     * 格式化格瓦拉电影院到数据库
     *
     */
    public function getGewaraCinemaToData(){

        for ($i=0; $i <= 19; $i++) { 
            $filePath = C('FETCH_INFO.FILE_PATH').'movie/movie'.$i.'.log';

            $res = $this->fetch->fetch($filePath, 'getGewaraCinema');

            foreach ($res as $k => $v) {
                $this->movieModel->addCinema($v);
            }
        }

    }

    /**
     * 下载格瓦拉电影院图片
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
     * 获取格瓦拉电影院图片地址
     * @param int $cinemaId 电影院id
     */
    private function _getGewaraCinemaImg($cinemaId = 0){
        $url = "http://www.gewara.com/cinema/cinemaPictureDetail.xhtml";

        $form = array(
                'cid'    => $cinemaId,
                'pvtype' => 'apic',
            );

        $imgJSON = $this->_submitForm($url, $form);

        $imgJSON = str_replace('var data = ', '', $imgJSON);

        $imgArr = json_decode($imgJSON, true);

        $returnImg = array();

        foreach ($imgArr['pictureList'] as $k => $v) {

            array_push($returnImg, $v['picturename']);
        }

        return $returnImg;
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
        $this->snoopy->agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';
    }

    /**
     * 初始化参数及配置信息
     *
     */
    private function _initParam(){
        import('Extend.FetchHTML');
        $this->fetch = new \FetchHTML('Movie');
    }

}
