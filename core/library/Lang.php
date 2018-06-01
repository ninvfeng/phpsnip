<?php
namespace core\library;
//语言
class Lang{

	//当前可用语言列表
	private $list=['zh-cn','en-us'];

	//当前语言
	private $current='zh-cn';

	public function __construct(){

        // 自动侦测设置获取语言选择
        if (isset($_GET['lang'])) {
            $current = strtolower($_GET['lang']);
        } elseif (isset($_COOKIE['lang'])) {
            $current = strtolower($_COOKIE['lang']);
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $current     = strtolower($matches[1]);
        }
        if(in_array($current, $this->list)){
        	$this->current=$current;
        	setcookie('lang',$this->current,time()+60*60*24*7);
        }

        //加载语言文件
        $this->lang = include APP_PATH.'/lang/'.$this->current.'.php';
	}

	//获取语言项
	public function get($key){
		return $this->lang[$key];
	}
}