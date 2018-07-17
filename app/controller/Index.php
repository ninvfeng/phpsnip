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

    //获取数据
    public function lists(){
        $kw=get('kw');
        if($kw){
            $data=db('snippet')->where('name like :kw',['kw'=>'%'.$kw.'%'])->order('used desc')->select();
        }else{
            $data=db('snippet')->order('used desc')->select();
        }
        
        foreach($data as $k => $v){
            $data[$k]['param']=json_decode($v['param'],true);
            $data[$k]['tag']=db('tag')
                ->field('tag.name')
                ->join('snippet_with_tag on snippet_with_tag.tag_id=tag.id')
                ->where(['snippet_with_tag.snippet_id'=>$v['id']])
                ->select();
        }
        json_response($data);
    }

    //使用量+1
    public function usedInc(){
        $id=post('id','require');
        $res=db('snippet')->where(['id'=>$id])->setInc('used',1);
        json_response($res);
    }

    //编辑
    public function edit(){
        $id=post('id');
        $name=post('name','require','请输入名称');
        $desc=post('desc','require','请输入描述');
        $param=post('param');
        $tag=post('tag');
        $code=post('code','require','请输入代码');
        //编辑
        if($id){
            $data['name']=$name;
            $data['desc']=$desc;
            $data['code']=$code;
            $data['param']=json_encode($param);
            $data['updated_at']=date('Y-m-d H:i:s');
            $res=db('snippet')->where(['id'=>$id,'user_id'=>userid()])->update($data);
        }

        //添加
        else{
            $data['name']=$name;
            $data['desc']=$desc;
            $data['code']=$code;
            $data['user_id']=userid();
            $data['param']=json_encode($param);
            $data['created_at']=date('Y-m-d H:i:s');
            $data['updated_at']=date('Y-m-d H:i:s');
            $res=$id=db('snippet')->insert($data);
        }

        //更新标签
        if($tag){
            foreach($tag as $k => $v){
                $tid=db('tag')->field('id')->where(['name'=>$v])->find();
                if(!$tid){
                    $tid=db('tag')->insert(['name'=>$v]);
                }
                $tids[]=$tid;
            }
            db('snippet_with_tag')->where(['snippet_id'=>$id])->delete();
            foreach($tids as $k => $v){
                db('snippet_with_tag')->insert(['snippet_id'=>$id,'tag_id'=>$v]);
            }
        }

        json_response($res);
    }
}
