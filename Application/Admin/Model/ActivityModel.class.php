<?php

namespace Admin\Model;
use Think\Model;

class ActivityModel extends Model {

    public function createActivity($name,$pic){
        $data['name'] = $name;
        $data['pic'] = $pic;
        $data['ctime'] = time();
        $data['status'] = 0;
        return M('Activity')->add($data);
    }
    
    public function addCoupon($activityId,$couponinfo){
        $couponinfo['activity_id'] = $activityId;
        return M('activity_coupon')->add($couponinfo);
    }


}
