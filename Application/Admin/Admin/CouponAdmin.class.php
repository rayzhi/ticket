<?php

namespace Admin\Admin;

class CouponAdmin extends CommonAdmin {

    public function homeAct(){
        $this->meta_title = '管理优惠券';
        $list = D('coupon')->order('id desc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    
    public function couponAddAct(){
        $id = I('get.id');
        if($id){
            $info = D('coupon')->where(array('id'=>$id))->find();
            $this->assign('headline','修改优惠券');
            $this->assign('info',$info);
            if(IS_POST){
                $info = I('post.');
                $info['begin_time'] = strtotime($info['begin_time']);
                $info['end_time'] = strtotime($info['end_time']);
                D('coupon')->where(array('id'=>$id))->save($info);
                $this->success("修改成功！", UC('Coupon/home'));
            }
        }else{
            $this->assign('headline','添加优惠券');
            if(IS_POST){
                $info = I('post.');
                $info['ctime'] = time();
                $info['begin_time'] = strtotime($info['begin_time']);
                $info['end_time'] = strtotime($info['end_time']);
                D('coupon')->add($info);
                $this->success("修改成功！", UC('Coupon/home'));
            }
        }
        
        $this->display();
    }

    public function couponDelAct(){
        $id = I('post.id');
        D('coupon')->where(array('id'=>$id))->delete();
        $this->success("删除成功");
    }

    public function lookupdetailAct(){
        $id = I('get.id');
        $list = D('user_coupon')->where(array('coupon_id'=>$id))->select();
        foreach($list as &$info){
            $info['name'] = D('user')->where(array('open_id'=>$info['open_id']))->getField('nickname');
        }
        $this->assign('list',$list);
        $this->display();
    }
    

}
