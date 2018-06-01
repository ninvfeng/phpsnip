<?php

use core\Container;

//框架核心文件
class App{


    // 应用开始时间
    public $beginTime;

    //应用内存初始占用
    public $beginMem;

    //路由信息
    public $route=[];

    //运行环境
    public $env;

    //容器对象实例
    public $container;

    //全局变量
    public $data;

    //最终结果, 根据调用方式返回或输出
    public $res;

    //启动框架
    static public function run(){
        return new self();
    }

    //构造方法
    public function __construct(){

        //记录开始时间与内存
        $this->beginTime   = microtime(true);
        $this->beginMem    = memory_get_usage();

        //注册核心类到容器
        $this->container = Container::getInstance();
        bind('app',$this);
        bind('validate',\think\Validate::class);
        bind('lang',\core\library\Lang::class);
        bind('hook',\app\Hook::class);

        //路由分发
        $this->env();
        $this->router();    }

    //适配当前运行环境
    public function env(){
        if(stripos($_SERVER['HTTP_HOST'], 'beta') !== false){
            $this->env = 'beta';
        }elseif(stripos($_SERVER['HTTP_HOST'], 'local') !== false){
            $this->env = 'local';
        }elseif(stripos($_SERVER['HTTP_HOST'], '.d') !== false){
            $this->env = 'local';
        }else{
            $this->env = 'online';
        }
    }

    //解析路由
    public function router(){

        //默认控制器方法
        $this->route['action']=$_GET['a']?$_GET['a']:'index';
        $this->route['controller']=$_GET['c']?$_GET['c']:'index';
        $this->route['dir']='';

        //解析path
        // dump($_SERVER['REQUEST_URI']);
        // die();
        $path=$_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:$_SERVER['request_uri'];
        if(strstr($_SERVER['REQUEST_URI'],'index.php',true)=='/' || !strstr($_SERVER['REQUEST_URI'],'index.php')){
            $path=str_replace('index.php','',$path);
            if(strstr($path,'?'))
                $path=trim(strstr($path,'?',true),'/');
            else
                $path=trim($path,'/');
            unset($_GET[$path]);
            if($path){
                $path=explode('/',$path);
                $this->route['action']=array_pop($path);
                $this->route['controller']=array_pop($path);
                $this->route['dir']=implode('/',$path);            
            }            
        }

        //组装path
        $this->route['path']=strtolower($this->route['dir']).'/'.strtolower($this->route['controller']).'/'.strtolower($this->route['action']);
        $this->route['path']=trim($this->route['path'],'/');

        //首字母大写
        $this->route['controller']=ucfirst($this->route['controller']);

        //控制器文件
        $this->route['file']=APP_PATH.'controller/'.$this->route['dir'].'/'.$this->route['controller'].'.php';

        //控制器类名
        $this->route['class']='\\app\\controller\\'.str_replace('/','\\',$this->route['dir']).($this->route['dir']?'\\':'').$this->route['controller'];
    }

    //分发请求 调用控制器方法
    public function dispatch(){
        app('hook')->beforeDispatch();
        $controller=app($this->route['class']);
        $reflect = new ReflectionMethod($controller,$this->route['action']);
        app()->res.=Container::getInstance()->invokeReflectMethod($controller, $reflect);
        app('hook')->afterDispatch();
        return $this;
    }

    //根据命名空间自动加载php文件
    static public function autoload($class){
        $class=str_replace('\\','/',$class);
        $file=PATH.'/'.$class.'.php';
        require $file;
    }

    //输出最终结果
    public function send(){
        echo app()->res;
        exit();
    }
}
