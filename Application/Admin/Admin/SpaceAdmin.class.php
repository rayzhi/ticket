<?php

namespace Admin\Admin;

class SpaceAdmin extends CommonAdmin {

    public function indexAct(){
        $this->meta_title = '管理场地';
        $list = D('SpaceArea')->order('sorder asc')->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function addSpaceAct(){
        if(IS_POST){
            $id = I('post.id');
            $data = I('post.');
            if($id){
                D('SpaceArea')->where(array('id'=>$id))->save($data);
                $this->success("修改成功！", UC('Space/index'));
            }else{
                D('SpaceArea')->add($data);
                $this->success("添加成功！", UC('Space/index'));
            }
        }

        $id = I('get.id');
        if($id){
            $info = D('SpaceArea')->where(array('id'=>$id))->find();
            $this->assign('info',$info);
            $this->assign('headline','修改场地');
        }
        else{
            $this->assign('headline','添加场地');
        }
        $this->display();
    }

    public function delSpaceAct(){
        if(IS_POST){
            $id = I('post.id');
            D('SpaceArea')->where(array('id'=>$id))->delete();
            $this->success("删除成功！", UC('Space/index'));
        }
    }
}
