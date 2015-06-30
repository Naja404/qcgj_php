<?php
/**
 * 景点爬虫
 */

namespace Api\Controller;
use Think\Controller;

class TravelController extends ApiController {
    
    // 景点模型
    public $travelModel;

    public $snoopy;

    public $fetch;

    public function _initialize(){  
        
        parent::_initialize();

        $this->travelModel = D('Travel');

        $this->_initParam();
        $this->_initSnoopy();
    }

    // public function setCache(){
    //     $list = $this->travelModel->getBrandList();

    //     foreach ($list as $k => $v) {
    //         cacheList('logo', $v);
    //     }
    // }

    /**
     * 获取景点数据-携程
     *
     */
    public function getTravel(){
        $arr = cacheList('travel_back');

        if ($arr['SightId'] < 0) {
            exit('no');
        }

        if ($arr['CoverImg']) {
            $this->travelModel->upTravel($arr['SightId'], array('image' => $arr['CoverImg']));
            exit('has');
        }

        $json = '{"SightId":'.$arr['SightId'].',"head":{"cid":"09031024310038477892","ctok":"","cver":"1.0","lang":"01","sid":"8888","syscode":"09","auth":null},"contentType":"json"}';
        // $json = '{"SightId":110670,"head":{"cid":"09031024310038477892","ctok":"","cver":"1.0","lang":"01","sid":"8888","syscode":"09","auth":null},"contentType":"json"}';

        $results = `curl --connect-timeout 15 -H "Content-Type:application/json" -sd '$json' http://m.ctrip.com/restapi/soa2/10159/json/GetSightDetailAggregate?_fxpcqlniredt=09031024310038477892`;

        $detail = json_decode($results, true);

        $data = array(
                'image'        => $detail['SightDetailAggregate']['ImageCoverUrl'],
            );

        $this->travelModel->upTravel($arr['SightId'], $data);

        cacheList('travel_back_1', $arr);
    }

    public function listview(){
        $list = $this->travelModel->getTravelList();

        foreach ($list as $k => $v) {
            echo $v['name'].'<br><img src="http://local.qcgj.com/Public/download/travel/image/'.$v['path'].'"><br><br><br>';
        }
    }

    /**
     * 下载图片
     *
     */
    public function getTravelImg(){
        $list = $this->travelModel->getTravelList();

        foreach ($list as $k => $v) {

            preg_match('/\w+.jpg/', $v['image'], $imageName);
            $this->_downloadImage($v['image'], C('FETCH_INFO.FILE_PATH').'travel/image/'.$imageName[0]);
            $update = array('path' => $imageName[0]);
            $this->travelModel->upTravel($v['sightid'], $update);
        }

    }

    /**
     * 获取景点数据url
     *
     */
    public function getTravel_bak(){

        for ($i=1; $i <= 33; $i++) { 
            
            $url = "http://m.mafengwo.cn/jd/10099/gonglve.html?page=".$i."&is_ajax=1";
            $fetchRes = $this->snoopy->fetch($url);

            $filePath = C('FETCH_INFO.FILE_PATH').'travel/url'.$i.'.log';
            
            $fileContent = json_decode($fetchRes->results, true);

            makeFetchFile($fileContent['html'], $filePath);

            $url = $this->fetch->fetch($filePath, 'getTravelUrl');

            cacheList('travelUrl', $url);
        }
    }

    /**
     * 下载景点详细页
     *
     */
    public function getTravelPage_bak(){

        $cache = cacheList('travelUrl');

        $url = "http://m.mafengwo.cn";
        
        foreach ($cache as $k => $v) {
            
            preg_match('/\d+/', $v, $number);

            $fetchRes = $this->snoopy->fetch($url.$v);

            makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'travel/detail_'.$number[0].'.log');

            $intro = str_replace('poi/', 'poi/intro_', $v);

            $fetchRes = $this->snoopy->fetch($url.$intro);

            makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'travel/intro_'.$number[0].'.log');

            $guide = str_replace('poi/', 'poi/guide_', $v);

            $fetchRes = $this->snoopy->fetch($url.$guide);

            makeFetchFile($fetchRes->results, C('FETCH_INFO.FILE_PATH').'travel/guide_'.$number[0].'.log');

            $this->travelModel->addTravelNumber($number[0]);
        }
    }


    /**
     * 下载景点图片
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
    private function _submitForm($url, $filePath = false){

        $this->snoopy->set_submit_multipart();
        $this->snoopy->submit($url, array(), $filePath);

        return $this->snoopy->results;
    }

    /**
     * 实例化snoopy
     *
     */
    private function _initSnoopy(){
        import('Extend.Snoopy');
        $this->snoopy = new \Snoopy();
        // $this->snoopy->rawheaders['enctype'] = 'multipart/form-data';
        $this->snoopy->rawheaders['Content-Type'] = 'application/json';
        $this->snoopy->rawheaders['Accept'] = 'application/json';
        $this->snoopy->agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B410 Safari/600.1.4';
    }

    /**
     * 初始化参数及配置信息
     *
     */
    private function _initParam(){
        import('Extend.FetchHTML');
        $this->fetch = new \FetchHTML('Travel');
    }

}
