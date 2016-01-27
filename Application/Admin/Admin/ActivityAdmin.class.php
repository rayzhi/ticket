<?php

namespace Admin\Admin;

class ActivityAdmin extends CommonAdmin {

    public function indexAct(){
        $this->meta_title = '管理活动';
        $list = D('activity')->where(array('status'=>0))->order('id desc')->select();
        foreach($list as &$info){
            $info['couponcount'] = D('activity_coupon')->where(array('activity_id'=>$info['id']))->count();
        }
        $this->assign('list',$list);
        $this->display();
    }

    public function activityDisableAct(){
        if(IS_POST){
            $id = I('post.id');
            D('activity')->where(array('id'=>$id))->save(array('status'=>1));
            $this->success("修改成功！", UC('Activity/index'));
        }
        
    }

    public function activityAddAct(){
        //表单处理
        if(IS_POST){
            $id = I('post.id');
            $name = I('post.name');
            $imgurl = I('post.logo');
            if($id){
                $resp = D('Activity')->where(array('id'=>$id))->save(array('name'=>$name,'pic'=>$imgurl));
                if($resp){
                    $this->success("修改成功！", UC('Activity/index'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                $resp = D('Activity')->createActivity($name,$imgurl);
                if($resp){
                    $this->success("添加成功！", UC('Activity/index'));
                }else{
                    $this->error('添加失败');
                }
            }
        }

        $id = I('get.id');
        if($id){
            $info = D('Activity')->where(array('id'=>$id))->find();
            $this->assign('info',$info);
            $img_upload = unifile_upload(array('name' => 'logo', 'content' => $info['pic'], 'table' => 'CourseTeacher', 'p_key' => 'id', 'field_name' => 'logo','module'=>'course'));
            $this->assign('img_upload', $img_upload);
            $this->assign('headline','修改活动');
        }
        else{
            $img_upload = unifile_upload(array('name' => 'logo', 'content' => '','module'=>'course'));
            $this->assign('img_upload', $img_upload);
            $this->assign('headline','添加活动');
        }
        
        
        $this->display();
    }


    public function lookupdetailAct(){
        $id = I('get.id');
        $list = D('activity_coupon')->where(array('activity_id'=>$id))->select();
        foreach($list as &$info){
            $count = D('user_activitycoupon')->where(array('activitycoupon_id'=>$info['id']))->count(1);
            $info['count'] = $count;
        }
        $this->assign('activity_id',$id);
        $this->assign('list',$list);
        $this->display();
    }
    
    public function addActivityCouponAct(){
        if(IS_POST){
            $id = I('post.id');
            $info = I('post.');
            $info['stime'] = strtotime($info['stime']);
            $info['etime'] = strtotime($info['etime']);
            if($id){
                D('activity_coupon')->where(array('id'=>$id))->save($info);
                $this->success("修改成功！", UC('Activity/lookupdetail?id='.$info['activity_id']));
            }else{
                D('activity_coupon')->add($info);
                $this->success("添加成功！", UC('Activity/lookupdetail?id='.$info['activity_id']));
            }
            
        }
        $id = I('get.id');
        $activity_id = I('get.activity_id');
        if($id){
            $info = D('activity_coupon')->where(array('id'=>$id))->find();
            $this->assign('info',$info);
            $this->assign('headline','修改优惠券');
        }else{
            $this->assign('headline','添加优惠券');
        }
        $this->assign('activity_id',$activity_id);
        $this->display();
    }

    public function delActivityCouponAct(){
        if(IS_POST){
            $id = I('post.id');
            D('activity_coupon')->where(array('id'=>$id))->delete();
            $this->success("删除成功");
        }
    }
}
