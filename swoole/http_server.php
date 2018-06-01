<?php

//设置时区 开启错误提示
date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors','On');

//定义常量
define('PATH',dirname(__FILE__).'/../');
define('APP_PATH',PATH.'app/');
define('CORE_PATH',PATH.'core/');
define('SWOOLE',true);

//加载核心
require CORE_PATH.'App.php';
require PATH.'vendor/autoload.php';
require CORE_PATH.'function.php';

//允许跨域
header("Access-Control-Allow-Origin: *");

//根据命名空间自动加载php文件
spl_autoload_register('App::autoload');

$cmd=$_SERVER['argv'][1]?$_SERVER['argv'][1]:'start';

if($cmd=='start'){
	start();
}elseif($cmd=='stop'){
	stop();
}elseif($cmd=='restart'){
	stop();
	sleep(1);
	start();
}

//启动服务
function start(){
	$http = new swoole_http_server(config('swoole_http_server.host'),config('swoole_http_server.port'));
	$http->set(config('swoole_http_server'));
	$http->on("start", function ($server) {
	    echo "Swoole http server is started\n";
	});

	$http->on("request", function ($request, $response) {
		if($request->server['request_uri']=='/favicon.ico'){
			$response->header('Content-Type', 'image/x-icon');
			$response->sendfile(__DIR__.$request->server['request_uri']);
		}else{
		    $_GET = $request->get;
		    $_POST = $request->post;
		    $_COOKIE = $request->cookie;
		    $_FILES = $request->files;
		    $_SERVER = $request->server;
		    App::run()->dispatch();
		    $response->end(app()->res);		
		}
	});

	$http->start();
}

//关闭服务
function stop(){
	$pid=@file_get_contents(config('swoole_http_server.pid_file'));
	if($pid){
		exec('kill '.$pid);
	}
}