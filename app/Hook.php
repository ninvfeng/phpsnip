<?php
namespace app;
//钩子, 用于做一些统一处理
class Hook{

	//控制器方法执行前
	public function beforeDispatch(){

		//忽略验证方法
		$list=[
			'index/index',
			'index/one',
			'index/test',
			'task/login'
		];

		//非登陆接口验证token
		if(!in_array(app()->route['path'],$list)){
			$token=$_SERVER['HTTP_TOKEN'];
			if(!$token){
				json_response(null,'请登陆',403);
			}
			$arr=explode('.',$token);
			$key=$arr[0].'.'.$arr[1];
			$user=cache($key);
			if(!$user){
				json_response(null,'登陆已过期，请从新登陆',403);
			}
			data('user',$user);
		}
	}

	//控制器方法执行后
	public function afterDispatch(){
		
	}
}