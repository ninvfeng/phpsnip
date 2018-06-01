<?php
namespace app\controller;

class Task{

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

    //任务列表
    public function index(){
        $res=db('task')->where(['user_id'=>data('user.id')])->order('done asc')->select();
        json_response($res);
    }

    //添加任务
    public function add(){
        $data['name']=post('name','require','请输入任务名称');
        $data['created_at']=date('Y-m-d H:i:s');
        $data['user_id']=data('user.id');
        $id=db('task')->insert($data);
        $res=db('task')->where(['id'=>$id])->find();
        json_response($res);
    }

    //完成|取消完成任务
    public function done(){
        $id=post('id','require','未选择任务');
        $data['done']=post('status','in:0,1','未确认任务状态'); //1完成 0取消完成
        $data['updated_at']=date('Y-m-d H:i:s');
        if($data['done']){
            $data['finished_at']=date('Y-m-d H:i:s');
        }
        $res=db('task')->where(['id'=>$id,'user_id'=>data('user.id')])->update($data);
        json_response($res);
    }

    //修改任务
    public function edit(){
        $id=post('id','require','未选择任务');
        $data['name']=post('name','require','请输入任务名称');
        $data['updated_at']=date('Y-m-d H:i:s');
        $res=db('task')->where(['id'=>$id,'user_id'=>data('user.id')])->update($data);
        json_response($res);
    }
}