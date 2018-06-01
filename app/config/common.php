<?php
//公共配置文件
return [

    'version'=>'0.1.0',

    'swoole_http_server'=>[
    	'host'=>'0.0.0.0',
    	'port'=>'8080',
    	'daemonize' => 1, //后台运行
    	'pid_file' => PATH.'/cache/swoole_http_server.pid', //配置pid文件用于关闭或重启服务
    ],

    'swoole_websocket'=>[
    	'host'=>'0.0.0.0',
    	'port'=>'8012',
    	'daemonize' => 1, //后台运行
    	'pid_file' => PATH.'/cache/swoole_websocket.pid', //配置pid文件用于关闭或重启服务
    ]
];
