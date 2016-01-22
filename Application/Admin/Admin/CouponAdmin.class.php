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
        $this->assign('headline','添加优惠券');
        if(IS_POST){
            $info = I('post.');
            $info['ctime'] = time();
            D('coupon');
            $this->success("修改成功！", UC('Coupon/home'));
        }
        $this->display();
    }

    public function couponDelAct(){
        $id = I('post.id');
        D('coupon')->where(array('id'=>$id))->delete();
        $this->success("删除成功");
    }
    

}
