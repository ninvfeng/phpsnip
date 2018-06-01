<?php
//------------------------
// 系统核心函数
//------------------------

use core\Container;
use core\library\Mysql;

/**
 * 调试函数
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  mixed $var 需要打印的变量
 * @return void 
 * @date   2018-04-09
 */
function dump($var){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

/**
 * 读取配置文件
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key 支持.分割 如config('mysql.host') 读取$config['mysql']['host']
 * @return mixed 
 * @date   2018-04-09
 */
function config($key=''){
    static $config;
    if(!$config){
        $config=require APP_PATH.'/config/'.'common.php';
        $env_config=require APP_PATH.'/config/'.app()->env.'.php';
        $config=array_merge($config,$env_config);
    }
    if($key){
        $key=explode('.',$key);
        $res=$config;
        foreach($key as $k => $v){
            $res=$res[$v];
        }
        return $res;        
    }else{
        return $config;
    }
}

/**
 * 快速获取容器中的实例 支持依赖注入
 * @param  string $name 类名或标识 默认获取当前应用实例
 * @param  array  $args 参数
 * @param  bool   $newInstance    是否每次创建新的实例
 * @return object
 */
function app($name = 'app', $args = [], $newInstance = false){
    return Container::get($name, $args, $newInstance);
}

/**
 * 绑定一个类到容器
 * @access public
 * @param  string $abstract 类标识、接口
 * @param  mixed  $concrete 要绑定的类、闭包或者实例
 * @return Container
 */
function bind($abstract, $concrete = null){
    return Container::getInstance()->bind($abstract, $concrete);
}

/**
 * 快速实例化数据库操作类
 * @author  ninvfeng <ninvfeng@qq.com>
 * @param   string $table 表名
 * @return  mixed 数据库对象 
 * @date    2018-04-09
 */
function db($table='null'){
    static $_db;
    if(!$_db){
        $_db=new Mysql(config('mysql'));
    }
    return $_db->table($table);
}


/**
 * 快速实例化model
 * @author  ninvfeng <ninvfeng@qq.com>
 * @param   string $model 模型
 * @return  mixed 模型对象 
 * @date    2018-04-09
 */
function model($model='null'){
    return app('app\\model\\'.$model,['config'=>config('mysql')]);
}

/**
 * 将调试信息写入数据库,方便不便直接浏览器打印调试
 * @author  ninvfeng <ninvfeng@qq.com>
 * @param   string $data 要打印的信息
 * @return  int
 * @date    2018-04-09
 */
function debug($data){
    if(is_array($data)){
        $data=json_encode($data);
    }
    return db('debug')->insert(['data'=>$data,'created_at'=>date('Y-m-d H:i:s')]);
}

/**
 * 快捷验证数据合法性
 * @author  ninvfeng <ninvfeng@qq.com>
 * @param   string $data 要验证的数据
 * @param   string $rule 验证规则 如 require必填 min:6最小长度6位 email邮箱等
 * @return  boolean
 * @date    2018-04-11
 */
function validate($data,$rule,$message='',$halt=false){
    $key='func';
    $res=app('validate')->rule([$key=>$rule])->message([$key=>$message])->check([$key=>$data]);
    if(!$res&&$halt){
        json_response(null,$message,401);
    }else{
        return $res; 
    }
}

/**
 * 快捷返回json格式数据
 * @author  ninvfeng <ninvfeng@qq.com>
 * @param   string $data 要返回的数据
 * @param   string $msg  该数据的提示信息
 * @param   string $code 错误码 200:正常
 * @return  mixed
 * @date    2018-04-11
 */
function json_response($data,$msg="数据为空",$code='400'){
    if(isset($data)&&(!empty($data))){
        $code=200;
        $msg='success';
    }
    header('Content-type: application/json');
    $arr = array(
        'data'=>$data,
        'code'=>$code,
        'message'=>$msg,
    );
    if(SWOOLE){
        app()->res.=json_encode($arr);
    }else{
        echo json_encode($arr);
        exit();
    }
}

/**
 * 快捷获取并验证$_GET参数
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key     参数名
 * @param  string $rule    验证规则
 * @param  string $message 验证错误提示
 * @return mixed
 * @date   2018-04-11
 */
function get($key='null',$rule='null',$message=''){
    if($key==='null'){
        return $_GET;
    }else{
        $key2=explode('.',$key);
        $res=$_GET;
        foreach($key2 as $k => $v){
            $res=$res[$v];
        }
        if($rule==='null'){
            return $res;
        }else{
            validate($res,$rule,$message,true);
            return $res;
        }
    }
}

/**
 * 快捷获取并验证$_POST参数
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key     参数名
 * @param  string $rule    验证规则
 * @param  string $message 验证错误提示
 * @return mixed
 * @date   2018-04-11
 */
function post($key='null',$rule='null',$message=[]){
    if($key==='null'){
        return $_POST;
    }else{
        $key2=explode('.',$key);
        $res=$_POST;
        foreach($key2 as $k => $v){
            $res=$res[$v];
        }
        if($rule==='null'){
            return $res;
        }else{
            validate($res,$rule,$message,true);
            return $res;
        }
    }
}

/**
 * 快速获取或设置session值
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key   session键 支持.分割 注意:当设置session值时不支持多个.连接
 * @param  string $value 要设置的session值
 * @return mixed
 * @date   2018-04-12
 */
function session($key='',$value=''){
    if(!$key){
        return $_SESSION;
    }
    if(!$value){
        $key=explode('.',$key);
        $res=$_SESSION;
        foreach($key as $k => $v){
            $res=$res[$v];
        }
        return $res;
    }else{
        if (strpos($key, '.')) {
            list($key1, $key2) = explode('.', $key);
            return $_SESSION[$key1][$key2]=$value;
        } else {
            return $_SESSION[$key]=$value;
        }
    }
}

/**
 * 快捷获取或设置cookie值
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key    cookie键
 * @param  string $key    要设置的cookie值
 * @param  int    $expire 过期时间,单位秒
 * @return mixed
 * @date   2018-04-12
 */
function cookie($key='',$value='',$expire=60*60*24*7){
    if(!$key){
        return $_COOKIE;
    }
    if(!$value){
        return $_COOKIE[$key];
    }else{
        return setcookie($key,$value,time()+$expire);
    }
}

/**
 * 快捷发起curl请求
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $url    请求地址
 * @param  array  $params 请求参数
 * @param  string $method 请求方法 GET或POST
 * @param  arrar  $ssl    使用证书 $ssl['cert']:证数  $ssl['key']:证数key
 * @return mixed 
 * @date   2018-04-11
 */
function http($url, $params = array(), $method = 'GET', $ssl = false){
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    );
    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            $getQuerys = !empty($params) ? '?'. http_build_query($params) : '';
            $opts[CURLOPT_URL] = $url . $getQuerys;
            break;
        case 'POST':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
    }
    if ($ssl) {
        $opts[CURLOPT_SSLCERTTYPE] = 'PEM';
        $opts[CURLOPT_SSLCERT]     = $ssl['cert'];
        $opts[CURLOPT_SSLKEYTYPE]  = 'PEM';
        $opts[CURLOPT_SSLKEY]      = $ssl['key'];;
    }
    /* 初始化并执行curl请求 */
    $ch     = curl_init();
    curl_setopt_array($ch, $opts);
    $data   = curl_exec($ch);
    $err    = curl_errno($ch);
    $errmsg = curl_error($ch);
    curl_close($ch);
    if ($err > 0) {
        $this->error = $errmsg;
        return false;
    }else {
        return $data;
    }
}

 /**
  * 使用twig快速渲染视图文件 简明教程:https://my.oschina.net/veekit/blog/268828
  * @author ninvfeng <ninvfeng@qq.com>
  * @param  string $view 视图文件
  * @param  string $data 视频数据
  * @return mixed
  * @date   2018-04-12
  */
function view($view,$data=[]){
    $loader = new Twig_Loader_Filesystem(APP_PATH.'/view');
    $twig = new Twig_Environment($loader);
    app()->res.=$twig->render($view.'.html',$data);
}

/**
 * 快捷使用语言包
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key 要获取的语言项
 * @return mixed
 * @date   2018-04-12
 */
function lang($key){
    return app('lang')->get($key);
}

/**
 * Redis 缓存快捷操作
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $key    缓存键
 * @param  string $value  缓存值
 * @param  string $expire 过期时间 默认7天
 * @return mixed
 * @date   2018-04-16
 */
function cache($key='null',$value='null',$expire=60*60*24*7){
    static $_redis;
    if(!$_redis){
        $_redis=new \Redis();
        $_redis->connect(config('redis.host'),config('redis.port'));
        if(config('redis.pwd')){
            $auth = $_redis->auth(config('redis.pwd'));
            if(!$auth){
                echo 'redis密码错误';
                die();
            }
        }
    }
    if($key==='null'){
        return $_redis;
    }
    $key=$_SERVER['SERVER_NAME'].':'.$key;
    if($value==='null'){
        $res = $_redis->get($key);
        if(is_null(json_decode($res))){
            return $res;
        }else{
            return json_decode($res,true);
        }
    }else{
        if(is_array($value)){
            $value=json_encode($value);
        }
        $res=$_redis->set($key,$value);
        $_redis->expire($key,$expire);
        return $res;
    }
}

/**
 * 驼峰命名转下划线命名 思路:小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string $str       要转换的字符串
 * @param  string $separator 分割符
 * @return mixed
 * @date   2018-04-19
 */
function uncamelize($str,$separator='_'){
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $str));
}

/**
 * 获取客户端真实IP
 * @author ninvfeng <ninvfeng@qq.com>
 * @return string
 * @date   2018-05-14
 */
function getip(){
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/**
 * 全局变量的设置与获取
 * @author ninvfeng <ninvfeng@qq.com>
 * @param  string  $key       键
 * @param  array   $value       键
 * @return string
 * @date   2018-05-14
 */
function data($key='null',$value='null'){
    if($key==='null'){
        return app()->data;
    }
    if($value==='null'){
        $key=explode('.',$key);
        $res=app()->data;
        foreach($key as $k => $v){
            $res=$res[$v];
        }
        return $res;
    }
    if (strpos($key, '.')) {
        list($key1, $key2) = explode('.', $key);
        return app()->data[$key1][$key2]=$value;
    } else {
        return app()->data[$key]=$value;
    }
}