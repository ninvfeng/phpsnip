<?php
/**
 * --------------------------------------------------------------------------
 * @desc livephp index控制器简单模拟
 * @pulish livephp
 * @time 2018-04-17
 * --------------------------------------------------------------------------
 */
namespace app\controller;
class Login{
    
    //登录
    public function login(){
        $username=post('username','require','请输入用户名');
        $password=post('password','require','请输入密码');
        if($user=db('user')->where(['username'=>$username])->find()){
            if(password_verify($password,$user['password'])){
                //生成token
                $cache_key='1.'.$user['id'];
                $token=$cache_key.'.'.md5(time().rand(0000,9999));
                $user['token']=$token;

                unset($user['password']);
                cache($cache_key,$user);

                //记录登录时间与IP
                $data['last_login_ip']=getip();
                $data['last_login_at']=date("Y-m-d H:i:s");
                db('user')->where(['id'=>$user['id']])->update($data);

                json_response($user);
            }
        }
    }

    //注册
    public function reg(){
        $username=post('username','require','请输入用户名');
        $password=post('password','require','请输入密码');
        $confirm_password=post('confirm_password','require','请再次输入密码');

        if($password!=$confirm_password){
            json_response('','两次密码不一样',4000);
        }

        if($user=db('user')->where(['username'=>$username])->find()){
            json_response('','该用户名已存在',4000);
        }

        $data['username']=$username;
        $data['password']=password_hash($password,PASSWORD_BCRYPT);
        $data['created_at']=date('Y-m-d H:i:s');
        $data['updated_at']=date('Y-m-d H:i:s');
        $data['last_login_at']=date('Y-m-d H:i:s');
        $data['last_login_ip']=getip();

        $uid=db('user')->insert($data);
        
        if($uid){
            $user=db('user')->where(['id'=>$uid])->find();
            //生成token
            $cache_key='1.'.$user['id'];
            $token=$cache_key.'.'.md5(time().rand(0000,9999));
            $user['token']=$token;

            unset($user['password']);
            cache($cache_key,$user);

            json_response($user);
        }
    }
}