<?php

namespace Web\Controller;
use Think\Controller;

class WebController extends Controller {

	public $ajaxRes;

	/**
	 * 初始化
	 */
    protected function _initialize(){

    	$this->ajaxRes = array(
					'status' => 1,
					'msg'    => L('ERR_PARAM'),
    		);
    }

}
