<?php
/**
 * --------------------------------------------------------------------------
 * @desc livephp index控制器简单模拟
 * @pulish livephp
 * @time 2018-04-17
 * --------------------------------------------------------------------------
 */
namespace app\controller;
class Index{

    //显示首页
    public function index(){
    	header('location:/index.html');
    }

    //获取1条数据
    public function one(){
    	$data=db('snippet')->find();
        $data['param']=json_decode($data['param'],true);
    	$data['tag']=db('tag')
	    	->field('tag.name')
    		->join('snippet_with_tag on snippet_with_tag.tag_id=tag.id')
    		->where(['snippet_with_tag.snippet_id'=>1])
    		->select();
    	json_response($data);
    }

    //使用量+1
    public function usedInc(){
        $id=post('id','require');
        $res=db('snippet')->where(['id'=>$id])->setInc('used',1);
        json_response($res);
    }
}
