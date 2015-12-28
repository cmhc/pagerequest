<?php
/*
Plugin Name: pagerequest
Plugin URI: http://imhuchao.com/tag/pagerequest
Description: call the external request with wordpress page。 用wordpress自带的页面调用外部的请求，支持get，post
Version: 0.0.1
Author: huchao
Author URI: http://imhuchao.com
License: GPLv2 or later
 */
add_shortcode('request', 'imrequest_shortcode');
function imrequest_shortcode($attr, $content) {
	if (!isset($attr['url'])) {
		return false;
	}

	$method = !isset($attr['method']) ? 'get' : $attr['method'];
	$url = $attr['url'];
	$timeout = !isset($attr['timeout']) ? 10 : $attr['timeout'];
	switch ($method) {
	//get方式调用
	case "get":
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'GET',
				'timeout' => (int)$timeout,
			),
		));
		$apicontent = file_get_contents($url, false ,$context);
		break;
	//post方式调用
	case "post":
		if (!isset($attr['data'])) {
			die("无post参数");
		}

		$attr['data'] = json_encode($attr['data']);

		if (empty($attr['data'])) {
			die('post参数需要json格式');
		}

		$data = http_build_query($attr['data']);
		$context = stream_context_create(array(
			'http' => array(
				'method' => "POST",
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($data) . "\r\n",
				'content' => $data,
				'timeout' => $timeout,
			),
		));
		$apicontent = file_get_contents($url,false, $context);
		break;
	}
	return $apicontent;
}
?>