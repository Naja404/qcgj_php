<?php

namespace Web\Controller;
use Think\Controller;

class IndexController extends Controller {

	public function index(){
		echo phpinfo();
	}
}
