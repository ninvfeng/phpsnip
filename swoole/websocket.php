<?php
//设置时区 开启错误提示
date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors','On');

//定义常量
define('PATH',dirname(__FILE__).'/../');
define('APP_PATH',PATH.'app/');
define('CORE_PATH',PATH.'core/');

//加载核心
require CORE_PATH.'App.php';
require PATH.'vendor/autoload.php';
require CORE_PATH.'function.php';

//允许跨域
header("Access-Control-Allow-Origin: *");

//根据命名空间自动加载php文件
spl_autoload_register('App::autoload');

app()->env='local';


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
    //创建websocket服务, 设置监听IP和端口
    $ws = new swoole_websocket_server(config('swoole_websocket.host'), config('swoole_websocket.port'));
    $ws->set(config('swoole_websocket'));
    $ws->on('open', function ($ws, $request) {
    });
    $ws->on('message', function ($ws, $frame) {
        parse_str($frame->data,$data);
        //初始化用户 必须包含 appid&userid
        if($data['type']=='init'){
            unset($data['type']);
            if((!$data['appid'])||(!$data['userid'])){
                $ws->push($frame->fd,'初始化必须包含appid和userid');
            }else{
                if(db('user')->where(['appid'=>$data['appid'],'userid'=>$data['userid']])->find()){
                    $data['sid']=$frame->fd;
                    db('user')->where(['appid'=>$data['appid'],'userid'=>$data['userid']])->update(['sid'=>$data['sid'],'data'=>json_encode($data)]);
                }else{
                    $data['sid']      = $frame->fd;
                    $insert['appid']  = $data['appid'];
                    $insert['userid'] = $data['userid'];
                    $insert['sid']    = $data['sid'];
                    $insert['token']  = md5(time().$data['sid']);
                    $insert['data']   = json_encode($data);
                    db('user')->insert($insert);
                }
                $from=db('user')->where(['appid'=>$data['appid'],'userid'=>$data['userid']])->find();
                $msg['type']='init';
                $msg['token']=$from['token'];
                $msg['data']=$from;
                $ws->push($frame->fd,http_build_query($msg));
            }
        }
        //收发消息 必须包含 token和要发送的用户id
        elseif($data['type']=='msg'){
            $from=db('user')->where(['token'=>$data['token']])->find();
            unset($from['token']);
            $to=db('user')->where(['appid'=>$from['appid'],'userid'=>$data['to']])->find();
            $msg['type']='msg';
            $msg['from']=$from;
            $msg['data']=$data['data'];
            $res=false;
            if($to['sid']){
                $res=$ws->push($to['sid'],http_build_query($msg));
            }else{
                $msg['type']='err';
                $msg['from']='system';
                $msg['data']='对方已离线';
                $ws->push($frame->fd,http_build_query($msg));
            }
            //是否保存记录
            if($data['save']){
                $log['appid']=$from['appid'];
                $log['from']=$from['userid'];
                $log['to']=$to['userid'];
                $log['data']=json_encode($data);
                $log['content']=json_encode($data['data']);
                $log['date']=date('Y-m-d H:i:s');
                $log['res']=$res;
                db('msg')->insert($log);
            }
        }
    });
    $ws->on('close', function ($ws, $fd) {
        db('user')->where(['sid'=>$fd])->update(['sid'=>0]);
    });
    $ws->start();    
}

//关闭服务
function stop(){
    $pid=@file_get_contents(config('swoole_websocket.pid_file'));
    if($pid){
        exec('kill '.$pid);
    }
}