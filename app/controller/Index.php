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

    public function index(){
    	header('location:/index.html');
    }

    public function one(){
    	// $res=db('snippet')->where(['id'=>1])->select();
    	// dump($res);
    	// $res=db('snippet')->query('select * from snippet where `id`=:id',['id'=>1]);
    	$res=db('snippet')->where(['id'=>1])->update(['name'=>'快捷发起http请求']);
    	dump($res);
    	// $data=db('snippet')->find();
    	// $data['tag']=db('tag')
	    // 	->field('tag.name')
    	// 	->join('snippet_with_tag on snippet_with_tag.tag_id=tag.id')
    	// 	->where(['snippet_with_tag.snippet_id'=>$data['id']])
    	// 	->select();
    	// json_response($data);
    }
}
