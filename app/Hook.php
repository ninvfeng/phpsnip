<?php
namespace app;
//钩子, 用于做一些统一处理
class Hook{

	//控制器方法执行前
	public function beforeDispatch(){

		//忽略验证方法
		$list=[
			'index/index',
			'index/lists',
			'index/usedinc',
			'index/test',
			'login/login',
			'login/reg',
		];

		//存在token获取用户信息
		$token=$_SERVER['HTTP_TOKEN'];
		if($token){
			$arr=explode('.',$token);
			$key=$arr[0].'.'.$arr[1];
			$user=cache($key);
			if($user['token']==$token){
				data('user',$user);
			}
		}


		//非登陆接口验证token
		if(!in_array(app()->route['path'],$list)&&!data('user')){
			json_response(null,'请登录',403);
		}
		
	}

	//控制器方法执行后
	public function afterDispatch(){
		
	}
}