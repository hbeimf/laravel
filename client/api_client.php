<?php

// https://www.jianshu.com/p/2988ba405b3b?from=timeline&isappinstalled=0
class Test {
	private $_token = '';

	function run() {
		// $this->reg();
		$this->login();
		$this->detail();
	}

	function reg(){
		// $url = 'http://la.demo.com/api/register/?name=test1&email=123456@qq.com&password=123456&c_password=123456';
		$url = 'http://la.demo.com/api/register/';
		$post_data['name']       = 'test1';
		$post_data['email']      = '123456@qq.com';
		$post_data['password']    = '123456';
		$post_data['c_password']    = '123456';
		//$post_data = array();
		$res = $this->request_post($url, $post_data);       
		print_r($res);
	 }

	 function login() {
	 	$url = 'http://la.demo.com/api/login/';
		// $post_data['name']       = 'test1';
		$post_data['email']      = '123456@qq.com';
		$post_data['password']    = '123456';
		// $post_data['c_password']    = '123456';
		//$post_data = array();
		$res = $this->request_post($url, $post_data);       
		// print_r($res);
		$arr = json_decode($res, true);
		print_r($arr);

		$this->_token = $arr['success']['token'];
	 }


	// Accept:application/json
	// Authorization:Bearer+空格+access_token
	 function detail() {
	 	$url = 'http://la.demo.com/api/details/';

	 	$post_data['email']      = '';
		$post_data['password']    = '';

	 	$header = [];
		$header[] = "Accept: application/json"; 
		$header[] = "Authorization: Bearer ".$this->_token; 

		// print_r($header);

	 	$res = $this->request_post_header($url, $post_data, $header);     
	 	var_dump($res);  
	 }

	  //https://www.cnblogs.com/ps-blog/p/6732448.html
	function request_post_header($url = '', $post_data = array(), $header = []) {
	    if (empty($url) || empty($post_data)) {
	        return false;
	    }
	    
	    $o = "";
	    foreach ( $post_data as $k => $v ) 
	    { 
	        $o.= "$k=" . urlencode( $v ). "&" ;
	    }
	    $post_data = substr($o,0,-1);

	    $postUrl = $url;
	    $curlPost = $post_data;
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);

	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 

	    $data = curl_exec($ch);//运行curl
	    curl_close($ch);
	    
	    return $data;
	}


	 //https://www.cnblogs.com/ps-blog/p/6732448.html
	function request_post($url = '', $post_data = array()) {
	    if (empty($url) || empty($post_data)) {
	        return false;
	    }
	    
	    $o = "";
	    foreach ( $post_data as $k => $v ) 
	    { 
	        $o.= "$k=" . urlencode( $v ). "&" ;
	    }
	    $post_data = substr($o,0,-1);

	    $postUrl = $url;
	    $curlPost = $post_data;
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	    $data = curl_exec($ch);//运行curl
	    curl_close($ch);
	    
	    return $data;
	}

}


$obj = new Test();
$obj->run();

?>