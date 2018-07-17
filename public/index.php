<?php

//设置时区 开启错误提示
date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors','On');

//定义常量
define('PATH',dirname(__FILE__).'/../');
define('APP_PATH',PATH.'app/');
define('CORE_PATH',PATH.'core/');
define('SWOOLE',false);

//加载核心
require CORE_PATH.'App.php';
require PATH.'vendor/autoload.php';
require CORE_PATH.'function.php';
require APP_PATH.'function.php';

//允许跨域
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Token");

//根据命名空间自动加载php文件
spl_autoload_register('App::autoload');

App::run()->dispatch()->send();

